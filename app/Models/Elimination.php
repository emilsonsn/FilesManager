<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Elimination extends Model
{
    use HasFactory;

    public $table = "eliminations";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'doc_number',
        'holder_name',
        'description',
        'box',
        'qtpasta',
        'cabinet',
        'drawer',
        'classification',
        'version',
        'situationAC',
        'situationAI',
        'initial_date',
        'archive_date',
        'expiration_date_A_C',
        'expiration_date_A_I',
        'observations',
        'tags',
        'temporality_id',
        'project_id',
        'user_id',
        'elimination_list_id',
    ];

    public function temporality(){
        return $this->belongsTo(Temporality::class);
    }

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function elimination_list(){
        return $this->belongsTo(EliminationList::class);
    }

}
