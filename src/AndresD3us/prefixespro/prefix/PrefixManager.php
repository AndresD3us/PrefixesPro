<?php

declare(strict_types=1);

namespace AndresD3us\prefixespro\prefix;

use AndresD3us\prefixespro\PrefixesPro;

class PrefixManager
{
    /** @var Prefix[] name => Prefix */
    private array $prefixes = [];

    public function __construct()
    {
        $this->loadFromProvider();
    }

    private function loadFromProvider(): void
    {
        foreach (PrefixesPro::getInstance()->getProvider()->getPrefixes() as $name => $data) {
            $this->prefixes[(string) $name] = new Prefix((string) $name, $data);
        }
    }

    public function reload(): void
    {
        $this->prefixes = [];
        $this->loadFromProvider();
    }

    /** @return Prefix[] */
    public function getAll(): array
    {
        return $this->prefixes;
    }

    /** @return Prefix[] */
    public function getByCategory(string $category): array
    {
        return array_filter(
            $this->prefixes,
            fn(Prefix $p) => $p->getCategory() === $category
        );
    }

    public function get(string $name): ?Prefix
    {
        return $this->prefixes[$name] ?? null;
    }

    public function exists(string $name): bool
    {
        return isset($this->prefixes[$name]);
    }

    public function create(string $name, string $format, string $category = "free"): Prefix
    {
        $prefix = new Prefix($name, [
            "format"   => $format,
            "category" => $category,
        ]);
        $this->prefixes[$name] = $prefix;
        return $prefix;
    }

    public function delete(string $name): bool
    {
        if (!$this->exists($name)) {
            return false;
        }

        unset($this->prefixes[$name]);

        $plugin   = PrefixesPro::getInstance();
        $provider = $plugin->getProvider();

        // Remove prefix from all sessions in memory AND persist each change
        foreach ($plugin->getSessionManager()->getAll() as $uuid => $session) {
            if ($session->hasPrefix($name) || $session->getActivePrefix() === $name) {
                $session->removePrefix($name);
                $provider->saveSession($session->getUuid(), $session->toArray());
            }
        }

        $path = $plugin->getDataFolder()
            . "prefixes" . DIRECTORY_SEPARATOR
            . $name . ".json";

        if (file_exists($path)) {
            unlink($path);
        }

        return true;
    }
}
