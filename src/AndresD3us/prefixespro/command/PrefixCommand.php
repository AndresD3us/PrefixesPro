<?php

declare(strict_types=1);

namespace AndresD3us\prefixespro\command;

use AndresD3us\prefixespro\command\subcommands\AddSubCommand;
use AndresD3us\prefixespro\command\subcommands\CreateSubCommand;
use AndresD3us\prefixespro\command\subcommands\DeleteSubCommand;
use AndresD3us\prefixespro\command\subcommands\HelpSubCommand;
use AndresD3us\prefixespro\command\subcommands\ListSubCommand;
use AndresD3us\prefixespro\command\subcommands\ReloadSubCommand;
use AndresD3us\prefixespro\command\subcommands\RemoveSubCommand;
use AndresD3us\prefixespro\PrefixesPro;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class PrefixCommand extends Command
{
    private array $subCommands = [];

    public function __construct()
    {
        parent::__construct(
            "prefix",
            "Commands for the prefix system",
            "/prefix <subcommand> [arguments]",
            ["pfx"]
        );
        $this->setPermission("prefixespro.command");
        $this->registerSubCommands();
    }

    private function registerSubCommands(): void
    {
        $subs = [
            new HelpSubCommand(),
            new CreateSubCommand(),
            new DeleteSubCommand(),
            new AddSubCommand(),
            new RemoveSubCommand(),
            new ListSubCommand(),
            new ReloadSubCommand(),
        ];

        foreach ($subs as $sub) {
            $this->subCommands[$sub->getName()] = $sub;
            foreach ($sub->getAliases() as $alias) {
                $this->subCommands[$alias] = $sub;
            }
        }
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermission($sender)) {
            return;
        }

        $subName = strtolower(array_shift($args) ?? "help");
        $sub     = $this->subCommands[$subName] ?? null;

        if ($sub === null) {
            $sender->sendMessage(PrefixesPro::getInstance()->getMessageManager()->get("unknown-subcommand"));
            return;
        }

        if (!$sender->hasPermission($sub->getPermission())) {
            $sender->sendMessage(PrefixesPro::getInstance()->getMessageManager()->get("no-permission"));
            return;
        }

        $sub->execute($sender, $args);
    }
}
