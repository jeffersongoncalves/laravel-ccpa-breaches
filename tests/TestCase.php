<?php

namespace JeffersonGoncalves\CcpaBreaches\Tests;

use JeffersonGoncalves\CcpaBreaches\CcpaBreachesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            CcpaBreachesServiceProvider::class,
        ];
    }
}
