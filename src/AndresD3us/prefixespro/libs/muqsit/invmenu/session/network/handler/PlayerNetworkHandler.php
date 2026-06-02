<?php

declare(strict_types=1);

namespace AndresD3us\libs\muqsit\invmenu\session\network\handler;

use Closure;
use AndresD3us\libs\muqsit\invmenu\session\network\NetworkStackLatencyEntry;

interface PlayerNetworkHandler{

	public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry;
}