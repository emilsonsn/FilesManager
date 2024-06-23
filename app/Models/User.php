<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'read_doc',
        'create_doc',
        'edit_doc',
        'delete_doc',
        'read_temporality',
        'create_temporality',
        'edit_temporality',
        'delete_temporality',
        'read_collection',
        'create_collection',
        'edit_collection',
        'delete_collection',
        'create_projects',
        'upload_limit',
        'is_active'
    ];

    public function projects(){
        return $this->hasMany(UserProject::class)->with('project');
    }

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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
