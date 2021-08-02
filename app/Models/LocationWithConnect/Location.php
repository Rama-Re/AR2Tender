<?php

namespace App\Models\LocationWithConnect;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'country_id',
        'location_name',
    ];

    protected $primaryKey = 'location_id';

    public function CompanyLocation(){
        return $this->hasMany(CompanyLocation::class,'location_id');
    }
    public function Country(){
        return $this->belongsTo(Country::class,'country_id');
    }

   
}
