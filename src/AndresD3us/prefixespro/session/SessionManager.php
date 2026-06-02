<?php

declare(strict_types=1);

namespace AndresD3us\prefixespro\session;

use AndresD3us\prefixespro\PrefixesPro;

class SessionManager
{
    private array $sessions = [];

    public function __construct()
    {
        $this->loadFromProvider();
    }

    private function loadFromProvider(): void
    {
        foreach (PrefixesPro::getInstance()->getProvider()->getSessions() as $uuid => $data) {
            $this->sessions[(string) $uuid] = new Session((string) $uuid, $data);
        }
    }

    public function reload(): void
    {
        $this->sessions = [];
        $this->loadFromProvider();
    }
    public function getAll(): array
    {
        return $this->sessions;
    }

    public function get(string $uuid): ?Session
    {
        return $this->sessions[$uuid] ?? null;
    }

    public function getByName(string $name): ?Session
    {
        $lower = strtolower($name);
        foreach ($this->sessions as $session) {
            if (strtolower($session->getName()) === $lower) {
                return $session;
            }
        }
        return null;
    }

    public function create(string $uuid, string $playerName): Session
    {
        $session = new Session($uuid, ["name" => $playerName, "ownedPrefixes" => [], "activePrefix" => ""]);
        $this->sessions[$uuid] = $session;
        return $session;
    }

    public function getOrCreate(string $uuid, string $playerName): Session
    {
        return $this->get($uuid) ?? $this->create($uuid, $playerName);
    }
}
