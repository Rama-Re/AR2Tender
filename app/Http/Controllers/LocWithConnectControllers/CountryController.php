<?php

namespace App\Http\Controllers\LocWithConnectControllers;

//use App\Assets\Country;
use App\Models\LocationWithConnect\Country;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static function save()
    {
        $generalTrait = new GeneralTrait;

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
            return $generalTrait->returnSuccessMessage('countries added succesfully');
        } else {
            return $generalTrait->returnData('countries',DB::table('countries')->get(),'countries already added');
        }
    }
}