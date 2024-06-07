<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentCollection extends Model
{
    use HasFactory;

    public $table = "document_collections";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'loan_date',
        'loan_author',
        'loan_receiver',
        'gender',
        'return_date',
        'return_author',
        'receiver_author',
        'document_id',
        'user_id',
    ];


    public function document(){
        return $this->belongsTo(Document::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

}
