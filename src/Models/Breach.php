<?php

namespace JeffersonGoncalves\CcpaBreaches\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * A breach of the security of the system (Cal. Civ. Code §1798.82(g)).
 *
 * @property string $title
 * @property string|null $what_happened
 * @property Carbon|null $occurred_at
 * @property Carbon $discovered_at
 * @property Carbon|null $due_at your own target; California sets no fixed number of days
 * @property list<string>|null $data_categories what information was involved (§1798.82(h))
 * @property int|null $affected_residents California residents
 * @property bool $encrypted
 * @property bool|null $notification_required null = not assessed yet
 * @property string|null $assessment
 * @property string|null $what_we_are_doing
 * @property string|null $what_you_can_do
 * @property string|null $law_enforcement_delay
 * @property Carbon|null $residents_notified_at
 * @property bool $substitute_notice
 * @property Carbon|null $attorney_general_notified_at
 * @property string|null $attorney_general_reference
 * @property Carbon|null $closed_at
 * @property Carbon $created_at
 */
class Breach extends Model
{
    protected $guarded = [];

    protected $attributes = [
        'encrypted' => false,
        'substitute_notice' => false,
    ];

    public function getTable(): string
    {
        return config('ccpa-breaches.table', 'ccpa_breaches');
    }

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'discovered_at' => 'datetime',
            'due_at' => 'datetime',
            'data_categories' => 'array',
            'affected_residents' => 'integer',
            'encrypted' => 'boolean',
            'notification_required' => 'boolean',
            'residents_notified_at' => 'datetime',
            'substitute_notice' => 'boolean',
            'attorney_general_notified_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    /** Not assessed yet, or notification required and residents not told. */
    public function owesResidentNotice(): bool
    {
        return $this->closed_at === null
            && $this->notification_required !== false
            && $this->residents_notified_at === null;
    }

    /** More than 500 California residents notified: submit a sample copy to the Attorney General (§1798.82(f)). */
    public function owesAttorneyGeneralNotice(): bool
    {
        return $this->closed_at === null
            && $this->notification_required === true
            && (int) $this->affected_residents > (int) config('ccpa-breaches.attorney_general_threshold', 500)
            && $this->attorney_general_notified_at === null;
    }

    /** Past your target and not delayed at the request of law enforcement (§1798.82(c)). */
    public function isOverdue(): bool
    {
        return $this->owesResidentNotice()
            && $this->law_enforcement_delay === null
            && $this->due_at?->isPast() === true;
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopePending(Builder $query): void
    {
        $threshold = (int) config('ccpa-breaches.attorney_general_threshold', 500);

        $query->whereNull('closed_at')->where(fn (Builder $q) => $q
            ->where(fn (Builder $q) => $q->whereNull('residents_notified_at')->where(fn (Builder $q) => $q->whereNull('notification_required')->orWhere('notification_required', true)))
            ->orWhere(fn (Builder $q) => $q->where('notification_required', true)->where('affected_residents', '>', $threshold)->whereNull('attorney_general_notified_at')));
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeOverdue(Builder $query): void
    {
        $query->whereNull('closed_at')
            ->whereNull('residents_notified_at')
            ->whereNull('law_enforcement_delay')
            ->where(fn (Builder $q) => $q->whereNull('notification_required')->orWhere('notification_required', true))
            ->whereNotNull('due_at')
            ->where('due_at', '<', now());
    }
}
