<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class GeneralTrait extends Controller
{
    public static function returnError($errNum, $msg)
    {
        return [
            'status' => false,
            'errNum' => $errNum,
            'msg' => $msg,
        ];
    }
    
    public static function returnSuccessMessage($msg = "", $errNum = "200")
    {
        return ['status' => true,
            'errNum' => $errNum,
            'msg' => $msg,
        ];
    }
    
    public static function returnData($key, $value, $msg = ""){
        return [
            'status' => true,
            'errNum' => "200",
            'msg' => $msg,
            $key => $value
        ];
    }
    
    public static function returnDataWithToken($key, $value, $token ,$msg = ""){
        return [
            'status' => true,
            'errNum' => "200",
            'msg' => $msg,
            'token' => $token,
            $key => $value
        ];
    }

    public static function returnValidationError( $validator,$code = 'E001'){
        return GeneralTrait::returnError($code,$validator->errors()->first());
    }

    public static function returnCodeAccordingToInput($validator)
    {
        $inputs = array_keys($validator->errors()->toArray());
        $code = GeneralTrait::getErrorCode($inputs[0]);
        return $code;
    }

    public static function getErrorCode($input)
    {
        if($input == "username"){
            return 'E001';
        }
        
        if($input == "password"){
            return 'E002';
        }

        if($input == "email"){
            return 'E003';
        }

        if($input == "phone"){
            return 'E004';
        }
        
        if($input == "phone_id"){
            return 'E005';
        }

        if($input == "confirm_code"){
            return 'E006';
        }

        if($input == "country_id"){
            return 'E007';
        }
        
        if($input == "type"){
            return 'E008';
        }
        if($input=="file"){
            return 'E009';
        }
        else return 'E000';
        
    }

    
}
