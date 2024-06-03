<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProject extends Model
{
    use HasFactory;

    public $table = "user_project";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'user_id',
        'project_id',
    ];

    public function user(){
        return $this->hasMany(User::class);
    }

    public function project(){
        return $this->belongsTo(Project::class);
    }
}
