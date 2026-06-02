<?php

declare(strict_types=1);

namespace AndresD3us\libs\muqsit\invmenu\type\util\builder;

use AndresD3us\libs\muqsit\invmenu\type\InvMenuType;

interface InvMenuTypeBuilder{

	public function build() : InvMenuType;
}