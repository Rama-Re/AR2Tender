<?php

namespace App\Models\LocationWithController;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'numcode_id',
        'code'
    ];

    
}
