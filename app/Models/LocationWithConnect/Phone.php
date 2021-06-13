<?php

namespace App\Models\LocationWithController;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_id',
        'phone_number',
        'company_id',
        'numcode_id',
    ];

    
}
