<?php

namespace App\Models\LocationWithController;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'location_name',
        'country_id'
    ];

    
}
