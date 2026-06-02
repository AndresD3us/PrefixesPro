<?php

declare(strict_types=1);

namespace AndresD3us\prefixespro\command\subcommands;

use AndresD3us\prefixespro\command\SubCommandInterface;
use AndresD3us\prefixespro\PrefixesPro;
use AndresD3us\prefixespro\utils\TimeParser;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class AddSubCommand implements SubCommandInterface
{
    public function getName(): string      { return "add"; }
    public function getAliases(): array    { return ["give", "assign"]; }
    public function getPermission(): string { return "prefixespro.command.add"; }

    public function execute(CommandSender $sender, array $args): void
    {
        $plugin  = PrefixesPro::getInstance();
        $msgs    = $plugin->getMessageManager();
        $sessMgr = $plugin->getSessionManager();
        $pfxMgr  = $plugin->getPrefixManager();

        if (count($args) < 2) {
            $sender->sendMessage("§cCorrect usage: §e/prefix add <player> <prefix> §7[time: 30d, 1h, 1m]");
            return;
        }

        [$playerName, $prefixName] = $args;
        $timeArg = $args[2] ?? null;

        if (!$pfxMgr->exists($prefixName)) {
            $sender->sendMessage($msgs->get("prefix-not-found", ["name" => $prefixName]));
            return;
        }

        $duration = TimeParser::parse($timeArg);
        if ($duration === null) {
            $sender->sendMessage("§cInvalid time format. Use: §e30d§c, §e1h§c, §e30m§c, §e1d12h§c or nothing for permanent.");
            return;
        }

        $target   = Server::getInstance()->getPlayerByPrefix($playerName);
        $session  = null;
        $realName = $playerName;

        if ($target instanceof Player) {
            $session  = $sessMgr->get((string) $target->getUniqueId());
            $realName = $target->getName();
        } else {
            $session  = $sessMgr->getByName($playerName);
            $realName = $session?->getName() ?? $playerName;
        }

        if ($session === null) {
            $sender->sendMessage($msgs->get("player-not-found", ["player" => $playerName]));
            return;
        }

        $session->addPrefix($prefixName, $duration);
        $plugin->getProvider()->saveSession($session->getUuid(), $session->toArray());

        $durationStr = $duration === 0 ? "Permanent" : TimeParser::format($duration);

        $sender->sendMessage($msgs->get("prefix-added", [
            "prefix"   => $prefixName,
            "player"   => $realName,
            "duration" => $durationStr,
        ]));

        if ($target instanceof Player && $target->isOnline()) {
            $target->sendMessage($msgs->get("prefix-received", [
                "prefix"   => $plugin->getPrefixManager()->get($prefixName)?->getColorizedFormat() ?? $prefixName,
                "duration" => $durationStr,
            ]));
        }
    }
}
