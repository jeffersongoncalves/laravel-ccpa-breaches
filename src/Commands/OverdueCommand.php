<?php

namespace JeffersonGoncalves\CcpaBreaches\Commands;

use Illuminate\Console\Command;
use JeffersonGoncalves\CcpaBreaches\Events\BreachOverdue;
use JeffersonGoncalves\CcpaBreaches\Models\Breach;

class OverdueCommand extends Command
{
    protected $signature = 'ccpa:breaches-overdue';

    protected $description = 'Dispatch BreachOverdue for every breach whose residents were not notified by your target date';

    public function handle(): int
    {
        $count = 0;

        Breach::query()->scopes('overdue')->lazyById()->each(function (Breach $breach) use (&$count): void {
            event(new BreachOverdue($breach));
            $count++;
        });

        $count === 0
            ? $this->info('No overdue breaches.')
            : $this->warn("{$count} breach(es) past your notification target.");

        return self::SUCCESS;
    }
}
