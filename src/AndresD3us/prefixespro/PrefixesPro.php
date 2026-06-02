<?php

declare(strict_types=1);

namespace AndresD3us\prefixespro;

use AndresD3us\prefixespro\command\PrefixCommand;
use AndresD3us\prefixespro\command\PrefixesCommand;
use AndresD3us\libs\AndresD3us\libs\muqsit\invmenu\InvMenuHandler;
use AndresD3us\prefixespro\listener\EventListener;
use AndresD3us\prefixespro\prefix\PrefixManager;
use AndresD3us\prefixespro\provider\Provider;
use AndresD3us\prefixespro\session\SessionManager;
use AndresD3us\prefixespro\utils\MessageManager;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;

class PrefixesPro extends PluginBase
{
    private static PrefixesPro $instance;

    private Provider       $provider;
    private PrefixManager  $prefixManager;
    private SessionManager $sessionManager;
    private MessageManager $messageManager;

    private bool $reloading = false;

    protected function onLoad(): void
    {
        self::$instance = $this;
    }

    public function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->saveResource("messages.yml");

        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }

        $this->provider       = new Provider();
        $this->messageManager = new MessageManager();
        $this->prefixManager  = new PrefixManager();
        $this->sessionManager = new SessionManager();

        $this->registerCommands();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

        $interval = (int) $this->getConfig()->get("autosave-interval", 300);
        $this->getScheduler()->scheduleRepeatingTask(
            new ClosureTask(fn() => $this->provider->save()),
            $interval * 20
        );

        $this->getLogger()->info("§aPrefixesPro v" . $this->getDescription()->getVersion() . " enabled successfully.");
    }

    public function onDisable(): void
    {
        if (isset($this->provider)) {
            $this->provider->save();
        }
        $this->getLogger()->info("§cPrefixesPro disabled. Data saved.");
    }

    private function registerCommands(): void
    {
        $map = $this->getServer()->getCommandMap();

        foreach (["prefix", "prefixes"] as $cmd) {
            $existing = $map->getCommand($cmd);
            if ($existing !== null) {
                $map->unregister($existing);
            }
        }

        $map->register("PrefixesPro", new PrefixCommand());
        $map->register("PrefixesPro", new PrefixesCommand());
    }

    public static function getInstance(): PrefixesPro { return self::$instance; }
    public function getProvider(): Provider            { return $this->provider; }
    public function getPrefixManager(): PrefixManager  { return $this->prefixManager; }
    public function getSessionManager(): SessionManager { return $this->sessionManager; }
    public function getMessageManager(): MessageManager { return $this->messageManager; }

    public function isReloading(): bool { return $this->reloading; }

    public function reload(): void
    {
        $this->reloading = true;
        $this->provider->reload();
        $this->messageManager->reload();
        $this->prefixManager->reload();
        $this->sessionManager->reload();

        $this->getScheduler()->scheduleDelayedTask(
            new ClosureTask(fn() => $this->reloading = false),
            60
        );
    }
}
