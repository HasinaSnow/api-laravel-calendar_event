<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Offer extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'infos', 'created_by', 'updated_by'];

    /**
     *  offer_pack (many to many)
     */
    public function packs(): BelongsToMany
    {
        return $this->belongsToMany(Invoice::class)
            ->withPivot('created_by', 'updated_by', 'created_at', 'updated_at');
        ;
    }

}
