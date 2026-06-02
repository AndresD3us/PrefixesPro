<?php

declare(strict_types=1);

namespace AndresD3us\libs\muqsit\invmenu\type\graphic\network;

use AndresD3us\libs\muqsit\invmenu\session\InvMenuInfo;
use AndresD3us\libs\muqsit\invmenu\session\PlayerSession;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;

interface InvMenuGraphicNetworkTranslator{

	public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void;
}