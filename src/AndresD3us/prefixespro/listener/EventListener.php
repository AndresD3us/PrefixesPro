<?php
declare(strict_types=1);

namespace AndresD3us\prefixespro\listener;

use AndresD3us\prefixespro\PrefixesPro;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\utils\TextFormat;
use pocketmine\Server;

class EventListener implements Listener
{
    public function onLogin(PlayerLoginEvent $event): void
    {
        $player  = $event->getPlayer();
        $uuid    = (string) $player->getUniqueId();
        $session = PrefixesPro::getInstance()->getSessionManager()->getOrCreate($uuid, $player->getName());
        if ($session->getName() !== $player->getName()) {
            $session->setName($player->getName());
        }
    }

    public function onQuit(PlayerQuitEvent $event): void
    {
        $player  = $event->getPlayer();
        $uuid    = (string) $player->getUniqueId();
        $plugin  = PrefixesPro::getInstance();
        $session = $plugin->getSessionManager()->get($uuid);
        if ($session !== null) {
            $plugin->getProvider()->saveSession($uuid, $session->toArray());
        }
    }

    public function onChat(PlayerChatEvent $event): void
    {
        $player  = $event->getPlayer();
        $plugin  = PrefixesPro::getInstance();
        $session = $plugin->getSessionManager()->get((string) $player->getUniqueId());

        $activePrefix = $session?->getActivePrefix();

        if ($activePrefix === null) {
            return;
        }

        $prefix = $plugin->getPrefixManager()->get($activePrefix);
        if ($prefix === null) {
            return;
        }

        $chatFormat = (string) $plugin->getConfig()->get("chat-format", "{prefix} §7{name}§r: {message}");

        $formatted = str_replace(
            ["{prefix}", "{name}", "{message}"],
            [$prefix->getColorizedFormat(), $player->getName(), $event->getMessage()],
            $chatFormat
        );

        $event->cancel();
        foreach (Server::getInstance()->getOnlinePlayers() as $recipient) {
            $recipient->sendMessage($formatted);
        }
        Server::getInstance()->getLogger()->info(TextFormat::clean($formatted));

        if ((bool) $plugin->getConfig()->get("update-nametag", false)) {
            $player->setNameTag($prefix->getColorizedFormat() . " " . $player->getName());
        }
    }
}