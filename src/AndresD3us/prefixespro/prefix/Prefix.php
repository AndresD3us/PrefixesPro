<?php

declare(strict_types=1);

namespace AndresD3us\prefixespro\prefix;

use pocketmine\utils\TextFormat;

class Prefix
{
    private string  $name;
    private string  $format;
    private string  $category;

    public function __construct(string $name, array $data)
    {
        $this->name     = $name;
        $this->format   = (string) ($data["format"]   ?? "");
        $cat = strtolower((string) ($data["category"] ?? "free"));
        $this->category = in_array($cat, ["free", "pay"], true) ? $cat : "free";
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getColorizedFormat(): string
    {
        return TextFormat::colorize($this->format);
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function isFree(): bool
    {
        return $this->category === "free";
    }

    public function toArray(): array
    {
        return [
            "format"   => $this->format,
            "category" => $this->category,
        ];
    }
}
