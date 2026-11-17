<?php

namespace JeffersonGoncalves\CcpaBreaches;

use DateTimeInterface;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\CcpaBreaches\Events\BreachOpened;
use JeffersonGoncalves\CcpaBreaches\Models\Breach;
use LogicException;

class BreachManager
{
    /**
     * Register a breach. The target date is your own (California sets no fixed number of days).
     *
     * @param  array<string, mixed>  $attributes  what_happened, occurred_at, data_categories, affected_residents, encrypted
     */
    public function open(string $title, ?DateTimeInterface $discoveredAt = null, array $attributes = []): Breach
    {
        $discoveredAt = Carbon::instance($discoveredAt ?? now());
        $target = config('ccpa-breaches.target_days');

        $breach = Breach::create([
            ...$attributes,
            'title' => $title,
            'discovered_at' => $discoveredAt,
            'due_at' => $target === null ? null : $discoveredAt->copy()->addDays((int) $target),
        ]);

        event(new BreachOpened($breach));

        return $breach;
    }

    /**
     * Record whether residents must be notified: unencrypted personal information as defined in
     * §1798.82(h), or encrypted data with its key, was (reasonably believed to be) acquired by an
     * unauthorized person. Keep the reasoning in $assessment.
     */
    public function assess(Breach $breach, bool $notificationRequired, ?string $assessment = null): Breach
    {
        return $this->update($breach, ['notification_required' => $notificationRequired, 'assessment' => $assessment ?? $breach->assessment]);
    }

    /**
     * Law enforcement determined that notifying would impede a criminal investigation (§1798.82(c)).
     * Notify as soon as they say it no longer would.
     */
    public function delayForLawEnforcement(Breach $breach, string $reason): Breach
    {
        return $this->update($breach, ['law_enforcement_delay' => $reason]);
    }

    /**
     * @param  bool  $substitute  substitute notice (email, website, statewide media) when allowed by §1798.82(j)
     */
    public function notifyResidents(Breach $breach, ?DateTimeInterface $at = null, bool $substitute = false): Breach
    {
        return $this->update($breach, ['residents_notified_at' => $at ?? now(), 'substitute_notice' => $substitute]);
    }

    /** Sample copy of the notice submitted to the Attorney General (§1798.82(f)). */
    public function notifyAttorneyGeneral(Breach $breach, ?string $reference = null, ?DateTimeInterface $at = null): Breach
    {
        return $this->update($breach, ['attorney_general_notified_at' => $at ?? now(), 'attorney_general_reference' => $reference]);
    }

    public function close(Breach $breach): Breach
    {
        if ($breach->notification_required === null) {
            throw new LogicException("Assess breach [{$breach->getKey()}] before closing it.");
        }

        if ($breach->owesResidentNotice() || $breach->owesAttorneyGeneralNotice()) {
            throw new LogicException("Breach [{$breach->getKey()}] still owes a notice.");
        }

        return $this->update($breach, ['closed_at' => now()]);
    }

    /**
     * The notice in the format of §1798.82(d)(1): title "Notice of Data Breach" and these headings, in this order.
     *
     * @return array<string, string|null>
     */
    public function notice(Breach $breach, string $contact): array
    {
        return [
            'title' => 'Notice of Data Breach',
            'What Happened?' => $breach->what_happened,
            'What Information Was Involved?' => implode(', ', $breach->data_categories ?? []),
            'What We Are Doing.' => $breach->what_we_are_doing,
            'What You Can Do.' => $breach->what_you_can_do,
            'For More Information.' => $contact,
        ];
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function update(Breach $breach, array $attributes): Breach
    {
        $breach->forceFill($attributes)->save();

        return $breach;
    }
}
