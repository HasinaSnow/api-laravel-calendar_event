<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Event extends Model
{
    use HasFactory;

    /**
     * Get all of the services for the event  (many to many polymorph)
     * 
     */
    // public function services(): MorphToMany
    // {
    //     return $this->morphToMany(Service::class, 'servable');
    // }
    /**
     * The services that belong to the Event (many to many) 
     *
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)
            ->withPivot('created_at', 'updated_at');
    }

    /**
     * get the parent eventable model category|type|place|confirmation|client (one to may polymorph)
     * 
     */
    // public function eventable(): MorphTo
    // {
    //     return $this->morphTo();
    // }

    /**
     * The tasks that belong to the Event (many to many) 
     *
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class)
            ->withPivot('check', 'attribute_to', 'created_by', 'updated_by', 'created_at', 'updated_at');
    }

    /**
     * The services that belong to the Event (many to many) 
     *
     */
    public function equipements(): BelongsToMany
    {
        return $this->belongsToMany(Equipement::class)
            ->withPivot('quantity', 'amount', 'created_by', 'updated_by', 'created_at', 'updated_at');
    }

    /**
     * Get the budget associated with the Event (one to one)
     * 
     */
    public function budget(): HasOne
    {
        return $this->hasOne(Budget::class);
    }

    /**
     * Get the invoice associated with the Event (one to one)
     * 
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Get the client that owns the Event (one to many)
     *
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(client::class);
    }

    /**
     * Get the category that owns the Event (one to many)
     *
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the place that owns the Event (one to many)
     *
     */
    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    /**
     * Get the confirmation that owns the Event (one to many)
     *
     */
    public function confirmation(): BelongsTo
    {
        return $this->belongsTo(Confirmation::class);
    }

    /**
     * Get the type that owns the Event (one to many)
     *
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    /**
     * Get the pack that owns the Event (one to many)
     */
    public function pack(): BelongsTo
    {
         return $this->belongsTo(Pack::class);
    }
}
