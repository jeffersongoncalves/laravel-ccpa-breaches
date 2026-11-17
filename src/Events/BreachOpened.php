<?php

namespace JeffersonGoncalves\CcpaBreaches\Events;

use JeffersonGoncalves\CcpaBreaches\Models\Breach;

/** Start the response: residents must be told without unreasonable delay. */
class BreachOpened
{
    public function __construct(public Breach $breach) {}
}
