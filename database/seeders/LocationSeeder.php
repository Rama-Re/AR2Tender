<?php

namespace Database\Seeders;

use App\Assets\City;
use App\Http\Controllers\LocWithConnectControllers\CityController;
use App\Http\Controllers\LocWithConnectControllers\LocationController;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LocationController::saveFav();
        //to save all cities
        // LocationController::saveAll();
    }
}
