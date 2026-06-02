<?php

declare(strict_types=1);

namespace AndresD3us\libs\muqsit\invmenu\type;

use AndresD3us\libs\muqsit\invmenu\InvMenu;
use AndresD3us\libs\muqsit\invmenu\type\graphic\InvMenuGraphic;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;

interface InvMenuType{

	public function createGraphic(InvMenu $menu, Player $player) : ?InvMenuGraphic;

	public function createInventory() : Inventory;
}