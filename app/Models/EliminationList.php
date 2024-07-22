<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EliminationList extends Model
{
    use HasFactory;

    public $table = "elimination_lists";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'organ',
        'unit',
        'responsible_selection',
        'responsible_unit',
        'president',
        'observations',
        'status',
        'project_id'
    ];

    public function files()
    {
        return $this->hasMany(EliminationListFile::class);
    }

}
