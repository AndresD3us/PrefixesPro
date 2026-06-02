<?php

declare(strict_types=1);

namespace AndresD3us\prefixespro\command\subcommands;

use AndresD3us\prefixespro\command\SubCommandInterface;
use AndresD3us\prefixespro\PrefixesPro;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ListSubCommand implements SubCommandInterface
{
    public function getName(): string      { return "list"; }
    public function getAliases(): array    { return ["all"]; }
    public function getPermission(): string { return "prefixespro.command.list"; }

    public function execute(CommandSender $sender, array $args): void
    {
        $plugin   = PrefixesPro::getInstance();
        $msgs     = $plugin->getMessageManager();
        $prefixes = $plugin->getPrefixManager()->getAll();

        if (empty($prefixes)) {
            $sender->sendMessage($msgs->get("list-empty"));
            return;
        }

        $sender->sendMessage($msgs->get("list-header", ["count" => count($prefixes)]));
        foreach ($prefixes as $name => $prefix) {
            $catColor = $prefix->isFree() ? "§a" : "§6";
            $sender->sendMessage(
                "  §7- §e$name §7» " . TextFormat::colorize($prefix->getFormat()) .
                " §8(§7category: {$catColor}" . strtoupper($prefix->getCategory()) . "§8)"
            );
        }
    }
}
