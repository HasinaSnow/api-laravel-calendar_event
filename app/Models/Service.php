<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    /**
     * The events that belong to the services (many to many) 
     *
     */
    public function events() : BelongsToMany
    {
        return $this->belongsToMany(Event::class)
            ->withPivot('created_by', 'updated_by');
    }

    /**
     * The users that belong to the services (many to many) 
     *
     */
    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('created_by', 'updated_by');
    }

    /**
     * Get the equipements for the event service (one to many)
     * 
     */
    public function equipements() : HasMany
    {
        return $this->hasMany(Equipement::class);
    }
}
