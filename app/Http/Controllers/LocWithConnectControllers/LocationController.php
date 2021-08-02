<?php

namespace App\Http\Controllers\LocWithConnectControllers;

use App\Assets\Locations\City1;
use App\Assets\Locations\City2;
use App\Assets\Locations\City3;
use App\Assets\Locations\City4;
use App\Assets\Locations\City5;
use App\Assets\Locations\City6;
use App\Assets\Locations\City7;
use App\Assets\Locations\City8;
use App\Assets\Locations\City9;
use App\Assets\Locations\FavCities;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Models\LocationWithController\Location;

//set_time_limit(1000);
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public static function get_fav_cities()
    {
        return FavCities::get_all();
    }
    public static function get_all_city($i)
    {
        switch ($i) {
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
                return (new GeneralTrait)->returnError('404', 'not a number refers to a city file');
        }

    }

    public static function saveFav()
    {
        $generalTrait = new GeneralTrait;
        if (empty(DB::table('locations')->count())) {
            $cities = self::get_fav_cities();
            foreach ($cities as $key => $value) {
                $citesarray = [
                    'country_id' => $value['country'],
                    'location_name' => $value['city'],
                ];
                DB::table('locations')->insert($citesarray);
            }
            return $generalTrait->returnSuccessMessage('cities added successfully');
        } else {
            return $generalTrait->returnError('403', 'the location table has elements');
        }
    }

    public static function saveAll()
    {
        $generalTrait = new GeneralTrait;
        if (empty(DB::table('locations')->count())) {
            for ($i = 1; $i < 10; $i++) {
                $cities = self::get_all_city($i);

                foreach ($cities as $key => $value) {
                    $citesarray = [
                        'country_id' => $value['country'],
                        'location_name' => $value['city'],
                    ];
                    DB::table('locations')->insert($citesarray);
                }
                sleep(65);
            }
            return $generalTrait->returnSuccessMessage('cities added successfully');
        } else {
            return $generalTrait->returnError('403', 'the location table has elements');
        }
    }

}
