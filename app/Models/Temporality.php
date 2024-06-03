<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Temporality extends Model
{
    use HasFactory;

    public $table = "temporalitys";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'code',
        'area',
        'function',
        'sub_function',
        'activity',
        'tipology',
        'current_custody_period',
        'intermediate_custody_period',
        'final_destination',
        'project_id',
    ];
}
