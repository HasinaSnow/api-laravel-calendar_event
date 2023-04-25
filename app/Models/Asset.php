<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = ['amount', 'created_by', 'updated_by'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function money(): BelongsTo
    {
        return $this->belongsTo(Money::class);
    }
}
