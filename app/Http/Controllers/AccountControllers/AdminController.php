<?php

namespace App\Http\Controllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Models\Account\Admin;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

use function GuzzleHttp\json_decode;

class AdminController extends Controller
{
    //use App\Http\Traits\GeneralTrait;
    //edit
    public static function validation(Request $request){
        $generalTrait = new GeneralTrait;
        try {
            $validator = Validator::make($request->only('admin_name','type'), [
                'admin_name' => 'required',
                'type'=>'required|in:admin'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                $code = $generalTrait->returnCodeAccordingToInput($validator);
                return $generalTrait->returnValidationError($code, $validator);
            }
            else return $generalTrait->returnSuccessMessage("validated");
        } catch (\Exception $e) {
            return $generalTrait->returnError($e->getCode(), $e->getMessage());
        }
    }
    
    public function getProfile(Request $request){
        $generalTrait = new GeneralTrait;
        $response = UserAuthController::validationToken($request);
        if($response["status"]){
            $result = UserAuthController::getUser($request);
            if($result["status"]){
                $admin = Admin::where('user_id',$result["user"]->user_id)->get();
                if (!$admin) {
                    return $generalTrait->returnError('404', 'not found');
                }
                return $generalTrait->returnData('admin', $admin);
            }
        }
        return $response;
    }

    public function register(Request $request)
    {
        
        $generalTrait = new GeneralTrait;
        $userC = new UserAuthController;
        $result = $userC->validation($request);
        if($result["status"]){
            $result2 = $this->validation($request);
            if($result2["status"]){
                //Request is valid, create new user
                $response = $userC->register($request);
                $admin = new Admin;
                $admin->admin_name = $request->admin_name;
                $admin->user_id = ($response["user"])->user_id;
                $admin->save();
                //Admin created, return success response
                return $generalTrait->returnSuccessMessage('Admin created successfully');
            }
            else return $result2;
        }
        else return $result;
        
    }

}
