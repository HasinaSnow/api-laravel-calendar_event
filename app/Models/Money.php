<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Money extends Model
{
    use HasFactory;

    /**
     * Get the journals that owns the Event (one to many)
     *
     */
    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class);
    }

    /**
     * Get the assets that owns the Event (one to many)
     *
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

}
