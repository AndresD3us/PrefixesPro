<?php

declare(strict_types=1);

namespace AndresD3us\prefixespro\provider;

use AndresD3us\prefixespro\PrefixesPro;

class Provider
{
    private string $dataFolder;
    private string $prefixesDir;
    private string $sessionsDir;

    public function __construct()
    {
        $this->dataFolder  = PrefixesPro::getInstance()->getDataFolder();
        $this->prefixesDir = $this->dataFolder . "prefixes" . DIRECTORY_SEPARATOR;
        $this->sessionsDir = $this->dataFolder . "sessions" . DIRECTORY_SEPARATOR;
        $this->ensureDirectories();
    }

    private function ensureDirectories(): void
    {
        foreach ([$this->prefixesDir, $this->sessionsDir] as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    public function reload(): void
    {
        PrefixesPro::getInstance()->getConfig()->reload();
    }

    /** @return array<string, array> */
    public function getPrefixes(): array
    {
        $prefixes = [];
        foreach (glob($this->prefixesDir . "*.json") ?: [] as $file) {
            $name = basename($file, ".json");
            $json = file_get_contents($file);
            if ($json !== false) {
                $data = json_decode($json, true);
                if (is_array($data)) {
                    $prefixes[$name] = $data;
                }
            }
        }
        return $prefixes;
    }

    public function savePrefix(string|int $name, array $data): void
    {
        file_put_contents(
            $this->prefixesDir . $name . ".json",
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    /** @return array<string, array> */
    public function getSessions(): array
    {
        $sessions = [];
        foreach (glob($this->sessionsDir . "*.json") ?: [] as $file) {
            $uuid = basename($file, ".json");
            $json = file_get_contents($file);
            if ($json !== false) {
                $data = json_decode($json, true);
                if (is_array($data)) {
                    $sessions[$uuid] = $data;
                }
            }
        }
        return $sessions;
    }

    public function saveSession(string $uuid, array $data): void
    {
        file_put_contents(
            $this->sessionsDir . $uuid . ".json",
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    public function save(): void
    {
        $plugin = PrefixesPro::getInstance();

        foreach ($plugin->getPrefixManager()->getAll() as $name => $prefix) {
            $this->savePrefix($name, $prefix->toArray());
        }

        foreach ($plugin->getSessionManager()->getAll() as $uuid => $session) {
            $this->saveSession($uuid, $session->toArray());
        }
    }
}
