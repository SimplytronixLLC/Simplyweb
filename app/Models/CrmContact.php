<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'quote_id',
        'visitor_id',
        'dolibarr_thirdparty_id',
        'stage',
        'engagement_score',
        'automation_enabled',
        'followup_step',
        'next_followup_at',
        'last_contacted_at',
        'source',
        'notes',
        'duplicate_of_id',
        'duplicate_reviewed',
        'email_status',
        'bounce_count',
        'last_bounced_at',
        'unsubscribed_at',
    ];

    protected $casts = [
        'automation_enabled' => 'boolean',
        'next_followup_at'   => 'datetime',
        'last_contacted_at'  => 'datetime',
        'last_bounced_at'    => 'datetime',
        'unsubscribed_at'    => 'datetime',
        'duplicate_reviewed'  => 'boolean',
    ];

    public const STAGES = ['new', 'contacted', 'quoted', 'won', 'lost'];

    // ---- Relationships -----------------------------------------------

    public function activities(): HasMany
    {
        return $this->hasMany(CrmActivity::class);
    }

    public function quote()
    {
        return $this->belongsTo(\App\Models\Quote::class, 'quote_id');
    }

    public function duplicateOf()
    {
        return $this->belongsTo(CrmContact::class, 'duplicate_of_id');
    }

    public function duplicates(): HasMany
    {
        return $this->hasMany(CrmContact::class, 'duplicate_of_id');
    }

    // ---- Scopes ---------------------------------------------------------

    public function scopeStage($query, string $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeSource($query, string $source)
    {
        return $query->where('source', $source);
    }

    /** Contacts due for an automated follow-up right now. */
    public function scopeDueForFollowup($query)
    {
        return $query->where('automation_enabled', true)
            ->whereNotIn('stage', ['won', 'lost'])
            ->where('email_status', '!=', 'bounced')
            ->where('manual_action', 'none')
            ->whereNotNull('next_followup_at')
            ->where('next_followup_at', '<=', now());
    }

    // ---- Stage transitions ------------------------------------------------

    /**
     * Moving a card to a new stage is a manual signal that stops further
     * automation for this contact (per the locked-in decision: "moving a
     * card to a new stage stops automation — that's how I step in").
     */
    public function moveToStage(string $stage, string $performedBy = 'admin'): void
    {
        $oldStage = $this->stage;

        $this->update([
            'stage' => $stage,
            'automation_enabled' => false,
        ]);

        $this->activities()->create([
            'type' => 'stage_change',
            'performed_by' => $performedBy,
            'meta' => ['old_stage' => $oldStage, 'new_stage' => $stage],
        ]);
    }

    public function logEmail(string $type, string $subject, string $body, string $performedBy = 'system', ?string $messageToken = null): CrmActivity
    {
        $this->update(['last_contacted_at' => now()]);

        return $this->activities()->create([
            'type' => $type,
            'subject' => $subject,
            'body' => $body,
            'performed_by' => $performedBy,
            'message_token' => $messageToken,
        ]);
    }

    /**
     * Setting a manual action is a human-touched-this-lead signal, same as
     * moveToStage() — halts automation.
     */
    public function setManualAction(string $action, ?string $note, string $performedBy = 'admin'): void
    {
        $this->update([
            'manual_action' => $action,
            'manual_action_at' => now(),
            'manual_action_note' => $note,
            'automation_enabled' => false,
        ]);

        $this->activities()->create([
            'type' => 'manual_action',
            'body' => $note,
            'performed_by' => $performedBy,
            'meta' => ['action' => $action],
        ]);
    }

    /**
     * Fold this contact's activity history into another contact, backfill
     * any missing fields on the primary, then delete this duplicate row.
     */
    public function mergeInto(CrmContact $primary): void
    {
        $this->activities()->update(['crm_contact_id' => $primary->id]);

        $backfill = [];
        foreach (['phone', 'company', 'quote_id', 'visitor_id', 'dolibarr_thirdparty_id'] as $field) {
            if (empty($primary->{$field}) && !empty($this->{$field})) {
                $backfill[$field] = $this->{$field};
            }
        }
        if (!empty($backfill)) {
            $primary->update($backfill);
        }

        $primary->activities()->create([
            'type' => 'manual_note',
            'body' => "Merged duplicate contact #{$this->id} ({$this->source}, {$this->email}) into this record.",
            'performed_by' => 'admin',
        ]);

        $this->delete();
    }

    /** Clear the duplicate flag and re-enable normal automation. */
    public function dismissDuplicateFlag(): void
    {
        $this->update([
            'duplicate_of_id' => null,
            'duplicate_reviewed' => true,
            'automation_enabled' => true,
            'next_followup_at' => now(),
        ]);
    }
}
