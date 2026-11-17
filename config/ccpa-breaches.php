<?php

return [

    'table' => 'ccpa_breaches',

    /*
     * California sets no fixed number of days: notify residents "in the most expedient time
     * possible and without unreasonable delay" (Cal. Civ. Code §1798.82(a)). Set your own
     * target here to get overdue alerts; null turns the alerts off.
     */
    'target_days' => 30,

    /*
     * Above this many California residents, also submit a sample copy of the notice to the
     * Attorney General (§1798.82(f)).
     */
    'attorney_general_threshold' => 500,

];
