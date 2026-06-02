<?php

declare(strict_types=1);

namespace AndresD3us\prefixespro\command;

use AndresD3us\libs\muqsit\invmenu\InvMenu;
use AndresD3us\libs\muqsit\invmenu\transaction\InvMenuTransaction;
use AndresD3us\libs\muqsit\invmenu\transaction\InvMenuTransactionResult;
use AndresD3us\libs\muqsit\invmenu\type\InvMenuTypeIds;
use AndresD3us\prefixespro\PrefixesPro;
use AndresD3us\prefixespro\prefix\Prefix;
use AndresD3us\prefixespro\utils\TimeParser;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PrefixesCommand extends Command
{
    public function __construct()
    {
        parent::__construct(
            "prefixes",
            "Opens the prefix selection menu",
            "/prefixes",
            ["pfxmenu"]
        );
        $this->setPermission("prefixespro.gui");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(PrefixesPro::getInstance()->getMessageManager()->get("only-in-game"));
            return;
        }

        if (!$this->testPermission($sender)) {
            return;
        }

        $this->openCategoryMenu($sender);
    }

    private function openCategoryMenu(Player $player): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->setName(TextFormat::colorize("&r&bPrefixes &eCategories"));

        $inv = $menu->getInventory();

        $filler = VanillaBlocks::STAINED_GLASS()->setColor(\pocketmine\block\utils\DyeColor::GRAY())->asItem()
            ->setCustomName(TextFormat::colorize("&r"));
        for ($i = 0; $i < 27; $i++) {
            $inv->setItem($i, $filler);
        }

        $freeBtn = VanillaBlocks::EMERALD()->asItem()
            ->setCustomName(TextFormat::colorize("&r&a&lFree Prefix"))
            ->setLore([
                TextFormat::colorize("&r&7Free prefixes available"),
                TextFormat::colorize("&r&7for all players."),
                " ",
                TextFormat::colorize("&r&aClick to view the list"),
            ]);
        $inv->setItem(11, $freeBtn);

        $payBtn = VanillaBlocks::GOLD()->asItem()
            ->setCustomName(TextFormat::colorize("&r&6&lPay Prefix"))
            ->setLore([
                TextFormat::colorize("&r&7Paid prefixes that you must"),
                TextFormat::colorize("&r&7have received in order to activate."),
                " ",
                TextFormat::colorize("&r&eClick to view your list"),
            ]);
        $inv->setItem(15, $payBtn);

        $menu->setListener(function (InvMenuTransaction $tx): InvMenuTransactionResult {
            $player = $tx->getPlayer();
            $item   = $tx->getItemClicked();

            if ($item->isNull()) return $tx->discard();

            $name = TextFormat::clean($item->getCustomName());

            if (str_contains($name, "Free Prefix")) {
                $player->removeCurrentWindow();
                PrefixesPro::getInstance()->getScheduler()->scheduleDelayedTask(
                    new \pocketmine\scheduler\ClosureTask(fn() => $this->openPrefixMenu($player, "free")),
                    2
                );
            } elseif (str_contains($name, "Pay Prefix")) {
                $player->removeCurrentWindow();
                PrefixesPro::getInstance()->getScheduler()->scheduleDelayedTask(
                    new \pocketmine\scheduler\ClosureTask(fn() => $this->openPrefixMenu($player, "pay")),
                    2
                );
            }

            return $tx->discard();
        });

        $menu->send($player);
    }

    private function openPrefixMenu(Player $player, string $category): void
    {
        $plugin   = PrefixesPro::getInstance();
        $msgs     = $plugin->getMessageManager();
        $pfxMgr   = $plugin->getPrefixManager();
        $sessMgr  = $plugin->getSessionManager();
        $session  = $sessMgr->get((string) $player->getUniqueId());

        $prefixes = $pfxMgr->getByCategory($category);

        $catLabel = $category === "free"
            ? TextFormat::colorize("&r&aFree Prefix")
            : TextFormat::colorize("&r&6Pay Prefix");

        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->setName(TextFormat::colorize("&r&bPrefixes &8» ") . $catLabel);

        $inv = $menu->getInventory();

        $filler = VanillaBlocks::STAINED_GLASS()->setColor(\pocketmine\block\utils\DyeColor::GRAY())->asItem()
            ->setCustomName(TextFormat::colorize("&r"));
        for ($i = 0; $i < 54; $i++) {
            $inv->setItem($i, $filler);
        }

        if (empty($prefixes)) {
            $inv->setItem(
                26,
                VanillaBlocks::BARRIER()->asItem()
                    ->setCustomName(TextFormat::colorize("&r&cNo prefixes in this category"))
                    ->setLore([TextFormat::colorize("&r&7Ask an admin to add prefixes.")])
            );
        }

        $slot = 10;
        foreach ($prefixes as $name => $prefix) {
            if ($slot >= 44) break;
            if ($slot % 9 === 0 || $slot % 9 === 8) {
                $slot++;
                continue;
            }

            $isOwned   = $session?->hasPrefix($name) ?? false;
            $isCurrent = $session?->getActivePrefix() === $name;

            $canUse = ($category === "free") || $isOwned;

            if ($category === "free") {
                $block = VanillaBlocks::EMERALD();
            } else {
                $block = $isOwned ? VanillaBlocks::GOLD() : VanillaBlocks::BARRIER();
            }

            $lore = [
                TextFormat::colorize("&r&8Prefix › &7" . strtoupper($category)),
                " ",
                TextFormat::colorize("&r&7Preview: " . $prefix->getColorizedFormat()),
            ];

            if ($isCurrent) {
                $lore[] = " ";
                $lore[] = TextFormat::colorize("&r&a✔ Active prefix");
            }

            if ($category === "pay") {
                if ($isOwned) {
                    $exp = $session->getExpireTimestamp($name);
                    $expStr = ($exp === 0) ? "§r§aPermanent" : "§e" . TimeParser::formatExpire($exp);
                    $lore[] = " ";
                    $lore[] = TextFormat::colorize("&r&7Time remaining: $expStr");
                } else {
                    $lore[] = " ";
                    $lore[] = TextFormat::colorize("&r&cYou do not have this prefix");
                }
            }

            if ($canUse && !$isCurrent) {
                $lore[] = " ";
                $lore[] = TextFormat::colorize("&r&eClick to activate it");
            } elseif ($isCurrent) {
                $lore[] = TextFormat::colorize("&r&eClick to deactivate it");
            }

            $inv->setItem(
                $slot,
                $block->asItem()
                    ->setCustomName(TextFormat::colorize("&e" . $name))
                    ->setLore($lore)
            );

            $slot++;
        }

        $inv->setItem(
            49,
            VanillaBlocks::BED()->asItem()
                ->setCustomName(TextFormat::colorize("&r&7Back"))
                ->setLore([TextFormat::colorize("&r&7Return to the category menu")])
        );

        $active = $session?->getActivePrefix();
        if ($active !== null && isset($prefixes[$active])) {
            $inv->setItem(
                45,
                VanillaBlocks::TNT()->asItem()
                    ->setCustomName(TextFormat::colorize("&r&cRemove active prefix"))
                    ->setLore([
                        TextFormat::colorize("&r&7Current: " . $pfxMgr->get($active)?->getColorizedFormat() ?? $active),
                        " ",
                        TextFormat::colorize("&r&eClick to remove it"),
                    ])
            );
        }

        $menu->setListener(function (InvMenuTransaction $tx) use ($msgs, $plugin, $category, $prefixes): InvMenuTransactionResult {
            $player  = $tx->getPlayer();
            $item    = $tx->getItemClicked();
            $session = $plugin->getSessionManager()->get((string) $player->getUniqueId());

            if ($item->isNull()) return $tx->discard();

            $rawName = TextFormat::clean($item->getCustomName());

            if (str_contains($rawName, "Back")) {
                $player->removeCurrentWindow();
                $plugin->getScheduler()->scheduleDelayedTask(
                    new \pocketmine\scheduler\ClosureTask(fn() => $this->openCategoryMenu($player)),
                    2
                );
                return $tx->discard();
            }

            if (str_contains($rawName, "Remove active prefix")) {
                if ($session !== null) {
                    $old = $session->getActivePrefix();
                    $session->setActivePrefix(null);
                    $plugin->getProvider()->saveSession($session->getUuid(), $session->toArray());
                    if ((bool) $plugin->getConfig()->get("update-nametag", false)) {
                        $player->setNameTag($player->getName());
                    }
                    $player->sendMessage($msgs->get("prefix-removed", [
                        "prefix" => $old ?? "?",
                        "player" => $player->getName(),
                    ]));
                    $player->removeCurrentWindow();
                    $plugin->getScheduler()->scheduleDelayedTask(
                        new \pocketmine\scheduler\ClosureTask(fn() => $this->openPrefixMenu($player, $category)),
                        2
                    );
                }
                return $tx->discard();
            }

            $prefix = $plugin->getPrefixManager()->get($rawName);
            if ($prefix === null) return $tx->discard();

            if ($category === "pay" && ($session === null || !$session->hasPrefix($rawName))) {
                $player->sendMessage($msgs->get("prefix-no-permission-use", ["prefix" => $rawName]));
                return $tx->discard();
            }

            if ($session !== null) {
                if ($session->getActivePrefix() === $rawName) {
                    $session->setActivePrefix(null);
                    $plugin->getProvider()->saveSession($session->getUuid(), $session->toArray());
                    if ((bool) $plugin->getConfig()->get("update-nametag", false)) {
                        $player->setNameTag($player->getName());
                    }
                    $player->sendMessage($msgs->get("prefix-removed", [
                        "prefix" => $rawName,
                        "player" => $player->getName(),
                    ]));
                } else {
                    if ($category === "free" && !$session->hasPrefix($rawName)) {
                        $session->addPrefix($rawName, 0);
                    }
                    $session->setActivePrefix($rawName);
                    $plugin->getProvider()->saveSession($session->getUuid(), $session->toArray());
                    $player->sendMessage($msgs->get("prefix-selected", ["prefix" => $prefix->getColorizedFormat()]));
                }

                $player->removeCurrentWindow();
                $plugin->getScheduler()->scheduleDelayedTask(
                    new \pocketmine\scheduler\ClosureTask(fn() => $this->openPrefixMenu($player, $category)),
                    2
                );
            }

            return $tx->discard();
        });

        $menu->send($player);
    }
}
