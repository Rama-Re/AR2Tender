<?php

namespace App\Http\Controllers\AccountControllers;

use App\Http\Controllers\AccountControllers\AdminController;
use App\Http\Controllers\AccountControllers\CompanyController;
use App\Http\Controllers\AccountControllers\EmployeeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Models\Account\Admin;
use App\Models\Account\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function PHPUnit\Framework\isNull;

class UserAuthController extends Controller
{
    //use GeneralTrait;
    public $state = null;
    public static function validation(Request $request){
        $generalTrait = new GeneralTrait;
        try {
            $data = $request->only('email', 'password', 'type');
            $validator = Validator::make($data, [
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:7|max:30',
                'type' => 'required|in:admin,company,employee'
            ]);
            
            //Send failed response if request is not valid
            if ($validator->fails()) {
                $code = $generalTrait->returnCodeAccordingToInput($validator);
                return $generalTrait->returnValidationError($code, $validator);
            }
            else return $generalTrait->returnSuccessMessage('validated');
        } catch (\Exception $e) {
            return $generalTrait->returnError($e->getCode(), $e->getMessage());
        }
    }
    public static function validationToken(Request $request){
        $generalTrait = new GeneralTrait;
        try {
            $validator = Validator::make($request->only('token'), [
                'token' => 'required',
            ]);
            
            //Send failed response if request is not valid
            if ($validator->fails()) {
                $code = $generalTrait->returnCodeAccordingToInput($validator);
                return $generalTrait->returnValidationError($code, $validator);
            }
            else return $generalTrait->returnSuccessMessage('validated');
        } catch (\Exception $e) {
            return $generalTrait->returnError($e->getCode(), $e->getMessage());
        }
    }
    public function register(Request $request)
    {
        $generalTrait = new GeneralTrait;
        $user = new User;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->type = $request->type;
        $user->confirm_code = 1234567;
        $user->save();
        return $generalTrait->returnData('user',$user);
    }
    //edit
    public function login(Request $request)
    {
        $generalTrait = new GeneralTrait;
        try {
            $validator = Validator::make($request->only('email', 'password'), [
                'email' => 'required|email',
                'password' => 'required',
            ]);
            //Send failed response if request is not valid
            if ($validator->fails()) {
                $code = $generalTrait->returnCodeAccordingToInput($validator);
                return $generalTrait->returnValidationError($code, $validator);
            }
            
            $credentials = $request->only(['email', 'password', 'type']);
            //check
            
            $token = JWTAuth::attempt($credentials);
            if (!$token) {
                return $generalTrait->returnError('E001', 'Email or Password is wrong');
            }
            $user = JWTAuth::user();
            return $generalTrait->returnDatawithToken('user', $user,"Logged in successfully",$token);
            
        } catch (\Exception $e) {
            return $generalTrait->returnError($e->getCode(), $e->getMessage());
        }
    }

    public static function getUser(Request $request)
    {
        $generalTrait = new GeneralTrait;
        $result = UserAuthController::validationToken($request);
        $user = JWTAuth::parseToken()->authenticate();
        if($user)
            return $generalTrait->returnData('user',$user);
        else return $generalTrait->returnError('401','Something went wrong');
    }

    public function logout(Request $request)
    {
        $validator = Validator::make($request->only('token'), [
            'token' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->returnError('200', 'some thing went wrong');
        }
        try {
            $token = $request->header('auth-token');
            JWTAuth::invalidate($token);
            return $this->returnSuccessMessage('Logged out successfully');
        } catch (JWTException $exception) {
            return $this->returnError('', 'Sorry, user cannot be logged out');
        }
    }
    
    public function resetPassword(Request $request)
    {
        //update user password, update token, return new token with
        //edit
    }
    public function verifyUser(Request $request)
    {
        //set confirm_code in user and sent it to email
        //edit
    }
    public function resendValidationCode(Request $request)
    {
        //update confirm_code in user and sent it to email
        //edit
    }

    public function confirmCode(Request $request)
    {
        //check verification code with confirm_code from user
        //edit
    }

}
