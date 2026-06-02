<?php

declare(strict_types=1);

namespace AndresD3us\prefixespro\command\subcommands;

use AndresD3us\prefixespro\command\SubCommandInterface;
use AndresD3us\prefixespro\PrefixesPro;
use pocketmine\command\CommandSender;

class ReloadSubCommand implements SubCommandInterface
{
    public function getName(): string      { return "reload"; }
    public function getAliases(): array    { return []; }
    public function getPermission(): string { return "prefixespro.command.reload"; }

    public function execute(CommandSender $sender, array $args): void
    {
        PrefixesPro::getInstance()->reload();
        $sender->sendMessage(PrefixesPro::getInstance()->getMessageManager()->get("reloaded"));
    }
}
