<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'role_id',
        'contact_number',
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
        'password' => 'hashed',
    ];

    // Relationship to Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // Helper methods for role checks
    public function isAdmin()
    {
        return is_object($this->role) && isset($this->role->name) && $this->role->name === 'admin';
    }

    public function isStaff()
    {
        return is_object($this->role) && isset($this->role->name) && $this->role->name === 'staff';
    }

    public function isFaculty()
    {
        return is_object($this->role) && isset($this->role->name) && $this->role->name === 'faculty';
    }
}
