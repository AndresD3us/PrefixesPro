<?php

declare(strict_types=1);

namespace AndresD3us\libs\muqsit\invmenu\session;

use AndresD3us\libs\muqsit\invmenu\InvMenu;
use AndresD3us\libs\muqsit\invmenu\type\graphic\InvMenuGraphic;

final class InvMenuInfo{

	public function __construct(
		readonly public InvMenu $menu,
		readonly public InvMenuGraphic $graphic,
		readonly public ?string $graphic_name
	){}
}