<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Client extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'infos',
    ];

    /**
     * Get all of the client's events (one to many polymorph)
     */
    // public function events() : MorphMany
    // {
    //     return $this->morphMany(Event::class, 'eventable');
    // }

    /**
     * Get the events for the event client. (one to many) 
     * 
     */
    public function events() : HasMany
    {
        return $this->hasMany(Event::class);
    }
}
