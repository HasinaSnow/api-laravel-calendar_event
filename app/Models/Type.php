<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Type extends Model
{
    use HasFactory;

    /**
     * Get all of the type's events (one to many polymorph)
     */
    // public function events() : MorphMany
    // {
    //     return $this->morphMany(Event::class, 'eventable');
    // }

    /**
     * Get all of the events for the Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
