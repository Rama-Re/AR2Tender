<?php

namespace App\Helpers;

class StringHelper
{

    public static function after($start, $str)
    {
        if (!is_bool(strpos($str, $start))) {
            return substr($str, strpos($str, $start) + strlen($start));
        }

    }

    public static function after_last($start, $str)
    {
        if (!is_bool(self::strrevpos($str, $start))) {
            return substr($str, self::strrevpos($str, $start));
        }
    }

    public static function before($end, $str)
    {
        return substr($str, 0, strpos($str, $end));
    }

    public static function before_last($end, $str)
    {
        return substr($str, 0, self::strrevpos($str, $end)- strlen($end));
    }
    

    public static function between($start, $end, $str)
    {
        return self::before($end, self::after($start, $str));
        
    }

    public static function between_last($start, $end, $str)
    {
        return self::after_last($start, self::before_last($end, $str));
    }
    

    public static function strrevpos($str, $thiss)
    {
        $rev_pos = strpos(strrev($str), strrev($thiss));
        if ($rev_pos === false) {
            return false;
        } else {
            return strlen($str) - $rev_pos ;
        }

    }

}
