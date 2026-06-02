<?php

declare(strict_types=1);

namespace AndresD3us\prefixespro\command\subcommands;

use AndresD3us\prefixespro\command\SubCommandInterface;
use pocketmine\command\CommandSender;

class HelpSubCommand implements SubCommandInterface
{
    public function getName(): string      { return "help"; }
    public function getAliases(): array    { return ["?", "h"]; }
    public function getPermission(): string { return "prefixespro.command"; }

    public function execute(CommandSender $sender, array $args): void
    {
        $lines = [
            "§8§m──────────────────────────────",
            "§b§lPrefixesPro §7- Commands",
            "§8§m──────────────────────────────",
            "§e/prefixes §7- Open prefix menu (GUI)",
            "§8§m──────────────────────────────",
            "§e/prefix create §7- Create prefix (FormAPI)",
            "§e/prefix add §7<player> <prefix> §8[§7time§8] §7- Give prefix",
            "  §8» §7No time = permanent | e.g.: §e30d§7, §e1h§7, §e30m",
            "§e/prefix revoke §7<player> <prefix> §7- Remove a prefix from a player",
            "§e/prefix delete §7<name> §7- Delete prefix from the server",
            "§e/prefix list §7- View all prefixes",
            "§e/prefix reload §7- Reload configuration",
            "§8§m──────────────────────────────",
        ];

        foreach ($lines as $line) {
            $sender->sendMessage($line);
        }
    }
}
