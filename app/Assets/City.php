<?php

namespace App\Assets;

use Exception;

class City {


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
            throw new Exception('not a number refers to a city file');
        }

    }

       
}

?>