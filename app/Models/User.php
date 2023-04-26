<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ]; 

    /**
     * Get all the services that belong to the User (many to many)
     * 
     */
    public function services() : BelongsToMany
    {
        return $this->belongsToMany(Service::class)
            ->withPivot('created_by', 'updated_by');
    }

    /**
     * Get all the permissions that belong to the User (many to many)
     *
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(permission::class)
            ->withPivot('created_at', 'updated_at');
    }

}
