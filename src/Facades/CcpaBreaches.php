<?php

namespace JeffersonGoncalves\CcpaBreaches\Facades;

use Illuminate\Support\Facades\Facade;
use JeffersonGoncalves\CcpaBreaches\BreachManager;

/**
 * @method static \JeffersonGoncalves\CcpaBreaches\Models\Breach open(string $title, ?\DateTimeInterface $discoveredAt = null, array<string, mixed> $attributes = [])
 * @method static \JeffersonGoncalves\CcpaBreaches\Models\Breach assess(\JeffersonGoncalves\CcpaBreaches\Models\Breach $breach, bool $notificationRequired, ?string $assessment = null)
 * @method static \JeffersonGoncalves\CcpaBreaches\Models\Breach delayForLawEnforcement(\JeffersonGoncalves\CcpaBreaches\Models\Breach $breach, string $reason)
 * @method static \JeffersonGoncalves\CcpaBreaches\Models\Breach notifyResidents(\JeffersonGoncalves\CcpaBreaches\Models\Breach $breach, ?\DateTimeInterface $at = null, bool $substitute = false)
 * @method static \JeffersonGoncalves\CcpaBreaches\Models\Breach notifyAttorneyGeneral(\JeffersonGoncalves\CcpaBreaches\Models\Breach $breach, ?string $reference = null, ?\DateTimeInterface $at = null)
 * @method static \JeffersonGoncalves\CcpaBreaches\Models\Breach close(\JeffersonGoncalves\CcpaBreaches\Models\Breach $breach)
 * @method static array<string, string|null> notice(\JeffersonGoncalves\CcpaBreaches\Models\Breach $breach, string $contact)
 *
 * @see BreachManager
 */
class CcpaBreaches extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BreachManager::class;
    }
}
