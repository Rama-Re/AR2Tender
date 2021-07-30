<?php

namespace Database\Seeders;

use App\Assets\Country;
use App\Http\Controllers\LocWithConnectControllers\CountryController;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CountryController::save();
    }
}
