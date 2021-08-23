<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MyValidator extends Controller
{
    public static function validation($data, $rules){
        try {
            $validator = Validator::make($data, $rules);
            //Send failed response if request is not valid
            if ($validator->fails()) {
                $code = GeneralTrait::returnCodeAccordingToInput($validator);
                return GeneralTrait::returnValidationError($validator,$code);
            }
            else return GeneralTrait::returnSuccessMessage('validated');
        } catch (\Exception $e) {
            return GeneralTrait::returnError($e->getCode(), $e->getMessage());
        }
    }
}
