<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Budget extends Model
{
    use HasFactory;
    
    protected $fillable = ['event_id', 'amount', 'infos', 'created_by', 'updated_by'];
    
    /**
     * Get the budget that ouns the Event (one to one)
     * 
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the payments for the event budget. (one to many) 
     * 
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
