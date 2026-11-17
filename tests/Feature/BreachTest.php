<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use JeffersonGoncalves\CcpaBreaches\Events\BreachOpened;
use JeffersonGoncalves\CcpaBreaches\Events\BreachOverdue;
use JeffersonGoncalves\CcpaBreaches\Facades\CcpaBreaches;
use JeffersonGoncalves\CcpaBreaches\Models\Breach;

it('opens a breach with your notification target', function () {
    Event::fake([BreachOpened::class]);
    $this->freezeSecond();

    $breach = CcpaBreaches::open('Backup exposed', now(), ['affected_residents' => 1200, 'data_categories' => ['name', 'SSN']]);

    expect($breach->due_at->equalTo(now()->addDays(30)))->toBeTrue()
        ->and($breach->owesResidentNotice())->toBeTrue()
        ->and(Breach::query()->scopes('pending')->count())->toBe(1);

    Event::assertDispatched(BreachOpened::class);
});

it('has no target when you turn it off', function () {
    config()->set('ccpa-breaches.target_days', null);

    expect(CcpaBreaches::open('Phishing')->due_at)->toBeNull();
});

it('owes nothing when notification is not required', function () {
    $breach = CcpaBreaches::assess(CcpaBreaches::open('Encrypted laptop', null, ['encrypted' => true]), false, 'Key not compromised.');

    expect($breach->owesResidentNotice())->toBeFalse()
        ->and(Breach::query()->scopes('pending')->count())->toBe(0);

    CcpaBreaches::close($breach);
    expect($breach->fresh()->closed_at)->not->toBeNull();
});

it('owes the Attorney General a sample above 500 residents', function () {
    $small = CcpaBreaches::notifyResidents(CcpaBreaches::assess(CcpaBreaches::open('Small', null, ['affected_residents' => 200]), true));
    $large = CcpaBreaches::notifyResidents(CcpaBreaches::assess(CcpaBreaches::open('Large', null, ['affected_residents' => 501]), true));

    expect($small->owesAttorneyGeneralNotice())->toBeFalse()
        ->and($large->owesAttorneyGeneralNotice())->toBeTrue()
        ->and(fn () => CcpaBreaches::close($large))->toThrow(LogicException::class, 'still owes');

    CcpaBreaches::notifyAttorneyGeneral($large, 'AG-2026-7');
    CcpaBreaches::close($large);

    expect($large->fresh())->attorney_general_reference->toBe('AG-2026-7')->closed_at->not->toBeNull();
});

it('pauses the clock for a law-enforcement delay', function () {
    Event::fake([BreachOverdue::class]);
    $delayed = CcpaBreaches::delayForLawEnforcement(CcpaBreaches::open('Delayed'), 'FBI asked us to wait.');
    CcpaBreaches::open('Late');

    $this->travel(31)->days();
    Artisan::call('ccpa:breaches-overdue');

    expect($delayed->fresh()->isOverdue())->toBeFalse();
    Event::assertDispatchedTimes(BreachOverdue::class, 1);
});

it('builds the Notice of Data Breach headings', function () {
    $breach = CcpaBreaches::open('Backup exposed', null, [
        'what_happened' => 'A backup was public for 2 days.',
        'data_categories' => ['name', 'Social Security number'],
        'what_we_are_doing' => 'We closed access and rotated keys.',
        'what_you_can_do' => 'Place a fraud alert.',
    ]);

    expect(array_keys(CcpaBreaches::notice($breach, 'privacy@acme.example')))->toBe([
        'title', 'What Happened?', 'What Information Was Involved?', 'What We Are Doing.', 'What You Can Do.', 'For More Information.',
    ])->and(CcpaBreaches::notice($breach, 'x')['What Information Was Involved?'])->toBe('name, Social Security number');
});

it('needs an assessment before closing', function () {
    CcpaBreaches::close(CcpaBreaches::open('Unknown'));
})->throws(LogicException::class, 'Assess');
