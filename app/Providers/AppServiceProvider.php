<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setUTCOffsetInDBConnection();
        //
    }

    private function setUTCOffsetInDBConnection() {

        $now = new \DateTime();
        $mins = $now->getOffset() / 60;
    
        $sgn = ($mins < 0 ? -1 : 1);
        $mins = abs($mins);
        $hrs = floor($mins / 60);
        $mins -= $hrs * 60;
    
        $offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);
    
        DB::statement("SET time_zone='".$offset."';");
    }
}
