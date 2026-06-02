<?php

declare(strict_types=1);

namespace AndresD3us\libs\formapi;

use pocketmine\form\Form as PmForm;
use pocketmine\player\Player;

abstract class Form implements PmForm
{
    protected array $data = [];
    private $callable;

    public function __construct(?callable $callable)
    {
        $this->callable = $callable;
    }

    public function handleResponse(Player $player, mixed $data): void
    {
        $this->processData($data);
        if (is_callable($this->callable)) {
            ($this->callable)($player, $data);
        }
    }

    abstract public function processData(mixed &$data): void;

    public function jsonSerialize(): array
    {
        return $this->data;
    }
}
