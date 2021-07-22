<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class GeneralTrait extends Controller
{
    public function returnError($errNum, $msg)
    {
        return [
            'status' => false,
            'errNum' => $errNum,
            'msg' => $msg,
        ];
    }
/*
    public function returnSuccessMessage($msg = "", $errNum = "5000")
    {
        return response()->json(['status' => true,
            'errNum' => $errNum,
            'msg' => $msg,
        ]);
    }
    */
    
    public function returnSuccessMessage($msg = "", $errNum = "200")
    {
        return ['status' => true,
            'errNum' => $errNum,
            'msg' => $msg,
        ];
    }
    
    /*
    public function returnData($key, $value, $msg = ""){
        return response()->json([
            'user'=> $value,
            'user_id' => $value->user_id

        ]);
    }
    */
    
    public function returnData($key, $value, $msg = ""){
        return [
            'status' => true,
            'errNum' => "200",
            'msg' => $msg,
            $key => $value
        ];
    }
    
    public function returnDatawithToken($key, $value, $msg = "", $token){
        return [
            'status' => true,
            'errNum' => "200",
            'msg' => $msg,
            'token' => $token,
            $key => $value
        ];
    }

    public function returnValidationError($code = 'E001', $validator){
        return $this->returnError($code,$validator->errors()->first());
    }

    public function returnCodeAccordingToInput($validator)
    {
        $inputs = array_keys($validator->errors()->toArray());
        $code = $this->getErrorCode($inputs[0]);
        return $code;
    }

    public function getErrorCode($input)
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
        else return 'E000';
        
    }

    
}
