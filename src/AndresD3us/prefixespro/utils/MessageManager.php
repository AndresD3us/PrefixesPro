<?php

declare(strict_types=1);

namespace AndresD3us\prefixespro\utils;

use AndresD3us\prefixespro\PrefixesPro;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class MessageManager
{
    private Config $config;
    private string $pluginPrefix;

    public function __construct()
    {
        $this->load();
    }

    private function load(): void
    {
        $this->config = new Config(
            PrefixesPro::getInstance()->getDataFolder() . "messages.yml",
            Config::YAML
        );
        $this->pluginPrefix = TextFormat::colorize(
            (string) $this->config->get("plugin-prefix", "&8[&bPrefixes&8]&r")
        );
    }

    public function reload(): void
    {
        $this->load();
    }

    public function get(string $key, array $replacements = []): string
    {
        $msg = (string) $this->config->get($key, "&cMessage not found: $key");
        $msg = str_replace("%plugin-prefix%", $this->pluginPrefix, $msg);

        foreach ($replacements as $k => $v) {
            $msg = str_replace("%$k%", (string) $v, $msg);
        }

        return TextFormat::colorize($msg);
    }
}
