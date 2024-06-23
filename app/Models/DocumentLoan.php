<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentLoan extends Model
{
    use HasFactory;

    public $table = "document_loan";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'document_id',
        'document_collection_id',
    ];


    public function document(){
        return $this->belongsTo(Document::class);
    }

    public function document_collection(){
        return $this->belongsTo(DocumentCollection::class);
    }


}
