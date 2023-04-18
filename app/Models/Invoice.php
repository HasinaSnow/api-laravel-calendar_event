<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['reference', 'event_id', 'infos', 'created_by', 'updated_by'];

    /**
     * Get the event that owns the invoice (one to one)
     * 
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

}
