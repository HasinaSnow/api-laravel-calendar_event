<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use HasFactory;

    /**
     * Get the budget that owns the payment (one to many)
     * 
     */
    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    /**
     * Get the parent paymentable model (deposit or remainder)
     * 
     */
    // public function paymentable() : MorphTo
    // {
    //     return $this->morphTo();
    // }

    public function deposit(): HasOne
    {
        return $this->hasOne(Deposit::class);
    }

    public function remainder(): HasOne
    {
        return $this->hasOne(Remainder::class);
    }

}
