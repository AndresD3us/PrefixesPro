<?php

declare(strict_types=1);

namespace AndresD3us\prefixespro\command\subcommands;

use AndresD3us\prefixespro\command\SubCommandInterface;
use AndresD3us\prefixespro\PrefixesPro;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class RemoveSubCommand implements SubCommandInterface
{
    public function getName(): string      { return "remove"; }
    public function getAliases(): array    { return ["take"]; }
    public function getPermission(): string { return "prefixespro.command.remove"; }

    public function execute(CommandSender $sender, array $args): void
    {
        $plugin  = PrefixesPro::getInstance();
        $msgs    = $plugin->getMessageManager();
        $sessMgr = $plugin->getSessionManager();

        if (count($args) < 2) {
            $sender->sendMessage("§cCorrect usage: §e/prefix remove <player> <prefix>");
            return;
        }

        [$playerName, $prefixName] = $args;

        $target  = Server::getInstance()->getPlayerByPrefix($playerName);
        $session = null;
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

        if (!$session->hasPrefix($prefixName)) {
            $sender->sendMessage("§cPlayer §e$realName §cdoes not have the prefix §e$prefixName§c.");
            return;
        }

        $session->removePrefix($prefixName);
        $plugin->getProvider()->saveSession($session->getUuid(), $session->toArray());

        $sender->sendMessage($msgs->get("prefix-removed", [
            "prefix" => $prefixName,
            "player" => $realName,
        ]));
    }
}
