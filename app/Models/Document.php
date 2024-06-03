<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    public $table = "documents";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'doc_number',
        'holder_name',
        'decription',
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
        'temporality_id',
        'project_id',
        'user_id',
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

    public function files(){
        return $this->hasMany(DocumentFile::class);
    }


}
