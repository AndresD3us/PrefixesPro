<?php
    
declare(strict_types=1);

namespace AndresD3us\prefixespro\session;

class Session
{
    private string  $uuid;
    private string  $name;
    private array   $ownedPrefixes = [];
    private ?string $activePrefix;
    public function __construct(string $uuid, array $data)
    {
        $this->uuid         = $uuid;
        $this->name         = (string) ($data["name"]          ?? "Unknown");
        $this->ownedPrefixes = (array)  ($data["ownedPrefixes"] ?? []);
        $ap = $data["activePrefix"] ?? null;
        $this->activePrefix = ($ap !== "" && $ap !== null) ? (string) $ap : null;
        $this->purgeExpired();
    }
    public function getUuid(): string   { return $this->uuid; }
    public function getName(): string   { return $this->name; }
    public function setName(string $n): void { $this->name = $n; }
    public function addPrefix(string|int $name, int $durationSeconds = 0): void
    {
        if ($durationSeconds <= 0) {
            $this->ownedPrefixes[$name] = 0;
        } else {
            $this->ownedPrefixes[$name] = time() + $durationSeconds;
        }
    }
    public function hasPrefix(string|int $name): bool
    {
        if (!isset($this->ownedPrefixes[$name])) return false;
        $exp = $this->ownedPrefixes[$name];
        if ($exp !== 0 && time() > $exp) {
            $this->removePrefix($name);
            return false;
        }
        return true;
    }
    public function removePrefix(string|int $name): void
    {
        unset($this->ownedPrefixes[$name]);
        if ($this->activePrefix === $name) {
            $this->activePrefix = null;
        }
    }
    public function getOwnedPrefixes(): array
    {
        $this->purgeExpired();
        return $this->ownedPrefixes;
    }
    public function getExpireTimestamp(string|int $name): ?int
    {
        return $this->ownedPrefixes[$name] ?? null;
    }
    public function getActivePrefix(): ?string
    {
        if ($this->activePrefix !== null && !$this->hasPrefix($this->activePrefix)) {
            $this->activePrefix = null;
        }
        return $this->activePrefix;
    }
    public function setActivePrefix(?string $name): void
    {
        $this->activePrefix = $name;
    }
    public function hasActivePrefix(): bool
    {
        return $this->getActivePrefix() !== null;
    }
    private function purgeExpired(): void
    {
        $now = time();
        foreach ($this->ownedPrefixes as $name => $exp) {
            if ($exp !== 0 && $now > $exp) {
                unset($this->ownedPrefixes[$name]);
                if ($this->activePrefix === $name) {
                    $this->activePrefix = null;
                }
            }
        }
    }
    public function toArray(): array
    {
        $this->purgeExpired();
        return [
            "name"          => $this->name,
            "ownedPrefixes" => $this->ownedPrefixes,
            "activePrefix"  => $this->activePrefix ?? "",
        ];
    }
}