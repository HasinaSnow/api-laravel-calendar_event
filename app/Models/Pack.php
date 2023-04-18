<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pack extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'infos', 'created_by', 'updated_by'];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * pack_offer (many to many)
     */
    public function offers(): BelongsToMany
    {
        return $this->belongsToMany(Offer::class)
            ->withPivot('created_by', 'updated_by', 'created_at', 'updated_at');
    }

}
