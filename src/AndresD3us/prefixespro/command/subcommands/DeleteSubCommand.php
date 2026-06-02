<?php

declare(strict_types=1);

namespace AndresD3us\prefixespro\command\subcommands;

use AndresD3us\prefixespro\command\SubCommandInterface;
use AndresD3us\prefixespro\PrefixesPro;
use pocketmine\command\CommandSender;

class DeleteSubCommand implements SubCommandInterface
{
    public function getName(): string      { return "delete"; }
    public function getAliases(): array    { return ["del"]; }
    public function getPermission(): string { return "prefixespro.command.delete"; }

    public function execute(CommandSender $sender, array $args): void
    {
        $plugin = PrefixesPro::getInstance();
        $msgs   = $plugin->getMessageManager();

        if (count($args) < 1) {
            $sender->sendMessage("§cCorrect usage: §e/prefix delete <name>");
            return;
        }

        $name = $args[0];

        if (!$plugin->getPrefixManager()->delete($name)) {
            $sender->sendMessage($msgs->get("prefix-not-found", ["name" => $name]));
            return;
        }

        $sender->sendMessage($msgs->get("prefix-deleted", ["name" => $name]));
    }
}
