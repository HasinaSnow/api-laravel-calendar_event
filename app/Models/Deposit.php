<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Deposit extends Model
{
    use HasFactory;

    /**
     * Get the deposit's payment (one to one polymorph) 
     * 
     */
    // public function payment() : MorphOne
    // {
    //     return $this->morphOne(Payment::class, 'paymentable');
    // }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(payment::class);
    }
}
