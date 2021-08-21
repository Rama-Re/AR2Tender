<?php

namespace App\Helpers;

class NumberHelper
{

    public static function readableSize($bytes)
    {
        if ($bytes < 1024) {
            return $bytes . " B";
        }
        $sizeInKB = $bytes / 1024;
        if ($sizeInKB > 1024) {
            $sizeInMB = $sizeInKB / 1024;
            if ($sizeInMB > 1024) {
                $sizeInGB = $sizeInMB / 1024;
                return round($sizeInGB, 2) . " GB";
            }
            return round($sizeInMB, 2) . " MB";
        } else {
            return round($sizeInKB, 2) . " KB";
        }

    }
}
