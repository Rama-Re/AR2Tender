<?php

namespace App\Http\Controllers\Location;

use App\Assets\Country;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    public function save()
    {
        if (empty(DB::table('countries')->count())) {
            $countries = Country::get_all();

            foreach ($countries as $key => $value) {
                $countriesarray = [
                    'country_id' => $key,
                    'country_name' => $value['name'],
                    'num_code' => $value['code'],
                ];
                DB::table('countries')->insert($countriesarray);
            }
        } else {
            throw new Exception('the countries table has elements');
        }

    }
}