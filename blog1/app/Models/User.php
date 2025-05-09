<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

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
        'password' => 'hashed',
    ];

    public function articles()
    {
        return $this->hasMany(Article::class)->onDelete('cascade');
    }

    public function components()
    {
        return $this->hasMany(Component::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    public function ownedGroups()
    {
        return $this->hasMany(Group::class, 'admin_id');
    }

    public function activeGroup()
    {
        return $this->groups()->latest()->first();
    }

    public function isMemberOf(Group $group)
    {
        return $this->groups()->where('group_id', $group->id)->exists();
    }
}
