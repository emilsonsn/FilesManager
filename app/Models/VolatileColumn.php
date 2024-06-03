<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolatileColumn extends Model
{
    use HasFactory;

    public $table = "volatile_columns";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'name',
        'value',
        'temporality_id',
    ];

    public function temporality(){
        return $this->belongsTo(Temporality::class);
    }    
}
