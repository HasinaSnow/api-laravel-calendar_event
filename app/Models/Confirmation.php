<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Confirmation extends Model
{
    use HasFactory;

    /**
     * Get all of the confirmation's events (one to many polymorph)
     */
    // public function events() : MorphMany
    // {
    //     return $this->morphMany(Event::class, 'eventable');
    // }

    /**
     * Get the events for the event confirmation. (one to many) 
     *
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

}
