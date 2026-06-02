<?php

declare(strict_types=1);

namespace AndresD3us\prefixespro\command\subcommands;

use AndresD3us\prefixespro\command\SubCommandInterface;
use AndresD3us\libs\formapi\CustomForm;
use AndresD3us\prefixespro\PrefixesPro;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class CreateSubCommand implements SubCommandInterface
{
    public function getName(): string      { return "create"; }
    public function getAliases(): array    { return ["new"]; }
    public function getPermission(): string { return "prefixespro.command.create"; }

    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cThis subcommand can only be used in-game.");
            return;
        }

        $this->openCreateForm($sender);
    }

    private function openCreateForm(Player $player): void
    {
        $plugin  = PrefixesPro::getInstance();
        $msgs    = $plugin->getMessageManager();
        $config  = $plugin->getConfig();

        $form = new CustomForm(function (Player $p, ?array $data) use ($plugin, $msgs, $config): void {
            if ($data === null) return;

            $name     = trim((string) ($data["name"]     ?? ""));
            $format   = trim((string) ($data["format"]   ?? ""));
            $category = (int) ($data["category"] ?? 0);

            $catStr = $category === 1 ? "pay" : "free";

            $manager = $plugin->getPrefixManager();

            if ($name === "" || $format === "") {
                $p->sendMessage($msgs->get("prefix-name-no-spaces"));
                $p->sendMessage("§cThe name and format cannot be empty.");
                return;
            }

            if ($manager->exists($name)) {
                $p->sendMessage($msgs->get("prefix-already-exists", ["name" => $name]));
                return;
            }

            $maxName = (int) $config->get("prefix-max-name-length", 10);
            if (strlen($name) > $maxName) {
                $p->sendMessage($msgs->get("prefix-name-too-long", ["max" => $maxName]));
                return;
            }

            $maxFormat = (int) $config->get("prefix-max-format-length", 30);
            if (strlen($format) > $maxFormat) {
                $p->sendMessage($msgs->get("prefix-format-too-long", ["max" => $maxFormat]));
                return;
            }

            if (str_contains($name, " ")) {
                $p->sendMessage($msgs->get("prefix-name-no-spaces"));
                return;
            }

            if (str_contains($format, " ")) {
                $p->sendMessage($msgs->get("prefix-format-no-spaces"));
                return;
            }

            $manager->create($name, $format, $catStr);
            $plugin->getProvider()->savePrefix($name, [
                "format"   => $format,
                "category" => $catStr,
            ]);

            $p->sendMessage($msgs->get("prefix-created", ["name" => $name]));
            $p->sendMessage("§7Category: §e" . strtoupper($catStr));
        });

        $form->setTitle("§lCreate new Prefix");
        $form->addLabel("§7Fill in the fields to create a new prefix.\n§8Colors use & (e.g.: &c[VIP]&r)", "info");
        $form->addInput("§lName §7(no spaces)", "e.g.: VIP", null, "name");
        $form->addInput("§lFormat §7(with &colors, no spaces)", "e.g.: &c[VIP]&r", null, "format");
        $form->addDropdown("§lCategory", ["Free (free - anyone can activate it)", "Pay (paid - assigned with /prefix add)"], 0, "category");

        $player->sendForm($form);
    }
}
