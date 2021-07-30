<?php

namespace App\Http\Controllers\LocWithConnectControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Models\LocationWithController\Location;
//set_time_limit(1000);
use Illuminate\Support\Facades\DB;


class LocationController extends Controller
{
    public static function save()
    {
        $generalTrait = new GeneralTrait;
        if (empty(DB::table('locations')->count())) {
            $cities = Location::get_fav_cities();
            foreach ($cities as $key => $value) {
                $citesarray = [
                    'country_id' => $value['country'],
                    'location_name' => $value['city'],
                ];
                DB::table('locations')->insert($citesarray);
            }
            return $generalTrait->returnSuccessMessage('cities added successfully');
        } else {
            return $generalTrait->returnError('403','the location table has elements');
        }
        /*
        if (empty(DB::table('locations')->count())) {
            for ($i = 1; $i < 10; $i++) {
                $cities = Location::get_all_city($i);

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
            return $generalTrait->returnError('403','the location table has elements');
        }
        */
    }
}