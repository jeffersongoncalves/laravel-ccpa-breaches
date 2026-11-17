<?php

namespace JeffersonGoncalves\CcpaBreaches\Events;

use JeffersonGoncalves\CcpaBreaches\Models\Breach;

/** Residents not notified by your target date, with no law-enforcement delay. */
class BreachOverdue
{
    public function __construct(public Breach $breach) {}
}
