<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EliminationListFile extends Model
{
    use HasFactory;

    public $table = "elimination_list_files";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'path',
        'name',
        'elimination_list_id',
    ];

    public function eliminationList()
    {
        return $this->belongsTo(EliminationList::class);
    }
}
