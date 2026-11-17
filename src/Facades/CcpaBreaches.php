<?php

namespace JeffersonGoncalves\CcpaBreaches\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JeffersonGoncalves\CcpaBreaches\CcpaBreaches
 */
class CcpaBreaches extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-ccpa-breaches';
    }
}
