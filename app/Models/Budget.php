<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Budget extends Model
{
    use HasFactory;
    
    protected $fillable = ['event_id', 'amount', 'infos', 'created_by', 'updated_by'];

    /**
     * Get all of the budget's journals.
     * 
     */
    public function journals(): MorphMany
    {
        return $this->morphMany(Journal::class, 'journalable');
    }
    
    /**
     * Get the budget that ouns the Event (one to one)
     * 
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

}
