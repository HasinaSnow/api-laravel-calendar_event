<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Equipement extends Model
{
    use HasFactory;

    /**
     * Get all of the equipement's journals.
     */
    public function journals(): MorphMany
    {
        return $this->morphMany(Journal::class, 'journalable');
    }

    /**
     * Get the service that owns the equipement (one to many) 
     * 
     */
    public function service() : BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * The events that belong to the equipement (many to many) 
     * 
     */
    public function events() : BelongsToMany
    {
        return $this->belongsToMany(Event::class)
        ->withPivot('quantity', 'amount', 'created_by', 'updated_by');
    }
}
