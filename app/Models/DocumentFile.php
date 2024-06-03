<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentFile extends Model
{
    use HasFactory;

    public $table = "document_files";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'document_id',
        'file_path',
        'name'
    ];


    public function document(){
        return $this->belongsTo(Document::class);
    }
}
