<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Journal extends Model
{
    use HasFactory;

    protected $fillable = ['wording', 'date', 'debit', 'amount', 'money_id', 'event_id', 'created_by', 'updated_by'];

    /**
     * Get the event that owns the Event (one to many)
     *
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the money that owns the Event (one to many)
     *
     */
    public function money(): BelongsTo
    {
        return $this->belongsTo(Money::class);
    }

    /**
     * Get the parent jouranlable model (budget or equipement)
     */
    public function journalable(): MorphTo
    {
        return $this->morphTo();
    }
}
