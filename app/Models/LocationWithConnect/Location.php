<?php

namespace App\Models\LocationWithConnect;

use App\Assets\City1;
use App\Assets\City2;
use App\Assets\City3;
use App\Assets\City4;
use App\Assets\City5;
use App\Assets\City6;
use App\Assets\City7;
use App\Assets\City8;
use App\Assets\City9;
use App\Assets\FavCities;
use App\Http\Controllers\GeneralTrait;
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

    public static function get_fav_cities(){
        return FavCities::get_all();
    }
    public static function get_all_city($i){
        switch ($i){
            case 1:
                return City1::get_all();
            case 2:
                return City2::get_all();
            case 3:
                return City3::get_all();
            case 4:
                return City4::get_all();
            case 5:
                return City5::get_all();
            case 6:
                return City6::get_all();
            case 7:
                return City7::get_all();
            case 8:
                return City8::get_all();
            case 9:
                return City9::get_all();
            default:
                return (new GeneralTrait)->returnError('404','not a number refers to a city file');
        }

    }
}
