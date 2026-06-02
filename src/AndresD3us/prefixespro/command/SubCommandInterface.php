<?php

declare(strict_types=1);

namespace AndresD3us\prefixespro\command;

use pocketmine\command\CommandSender;

interface SubCommandInterface
{
    public function getName(): string;

    /** @return string[] */
    public function getAliases(): array;

    public function getPermission(): string;

    public function execute(CommandSender $sender, array $args): void;
}
