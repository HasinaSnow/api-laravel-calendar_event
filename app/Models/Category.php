<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Category extends Model
{
    use HasFactory;

    /**
     * Get all of the category's events (one to many polymorph)
     */
    // public function events() : MorphMany
    // {
    //     return $this->morphMany(Event::class, 'eventable');
    // }

    /**
     * Get the events for the event category. (one to many) 
     *
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
