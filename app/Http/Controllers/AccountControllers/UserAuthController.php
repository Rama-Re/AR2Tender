<?php

namespace App\Http\Controllers\AccountControllers;

use App\Http\Controllers\AccountControllers\AdminController;
use App\Http\Controllers\AccountControllers\CompanyController;
use App\Http\Controllers\AccountControllers\EmployeeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Http\Controllers\MyValidator;
use App\Models\Account\Admin;
use App\Models\Account\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Exception;
use GrahamCampbell\ResultType\Result;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use function PHPUnit\Framework\isNull;

class UserAuthController extends Controller
{
    public static function validation(Request $request)
    {
        return MyValidator::validation(
            $request->only('email', 'password', 'type'),
            [
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:7|max:30',
                'type' => 'required|in:admin,company,employee'
            ]
            );
    }
    public static function validationToken(Request $request)
    {
        return MyValidator::validation($request->only('token'), [
            'token' => 'required',
        ]);
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
    public function login(Request $request)
    {
        $result = MyValidator::validation($request->only('email', 'password'), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $generalTrait = new GeneralTrait;
        if(!$result['status']){
            try{
                $credentials = $request->only(['email', 'password', 'type']);
                //check
                
                $token = JWTAuth::attempt($credentials);
                if (!$token) {
                    return $generalTrait->returnError('E001', 'Email or Password is wrong');
                }
                $user = JWTAuth::user();
                return $generalTrait->returnDatawithToken('user', $user,$token,"Logged in successfully");
            } catch (\Exception $e){
                return $generalTrait->returnError($e->getCode(), $e->getMessage());
            }
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
        $generalTrait = new GeneralTrait;
        $result = $this->validationToken($request);
        if($result['status']){
            $user = JWTAuth::parseToken()->authenticate();
            if($user) {
                $user->is_verified = false;
                $user->save();
                try {
                    JWTAuth::invalidate(JWTAuth::getToken());
                    return $generalTrait->returnSuccessMessage('Logged out successfully');
                } catch (JWTException $exception) {
                    return $generalTrait->returnError($exception->getCode(), $exception->getMessage());
                }
            }
            else return $generalTrait->returnError('401','Unauthenticated');
        }
        return $result;
    }
    
    public function verifyAccount(Request $request)
    {
        $generalTrait = new GeneralTrait;
        try{
            $confirmCode = random_int(100000,999999);
            $details = ['title'=> 'Use this code to confirm your Account','body'=> $confirmCode];
            Mail::to($request->email)->send(new \App\Mail\SampleMail($details));
            $user = User::where('email',$request->email)->get()->first();
            $user->confirm_code = $confirmCode;
            $user->save();
            return $generalTrait->returnSuccessMessage('Code send successfully');
        }catch(Exception $exception){
            return $generalTrait->returnError($exception->getCode(), $exception->getMessage());
        }
    }

    public function forgetPassword(Request $request)
    {
        $generalTrait = new GeneralTrait;
        try{
            $confirmCode = random_int(100000,999999);
            $details = ['title'=> 'Use this code to reset your Password','body'=> $confirmCode];
            Mail::to($request->email)->send(new \App\Mail\SampleMail($details));
            $user = User::where('email',$request->email)->get()->first();
            $user->confirm_code = $confirmCode;
            $user->save();
            return $generalTrait->returnSuccessMessage('Code send successfully');
        }catch(Exception $exception){
            return $generalTrait->returnError($exception->getCode(), $exception->getMessage());
        }
    }
    
    public function resetPassword(Request $request)
    {
        $generalTrait = new GeneralTrait;
        try {
            $validator = Validator::make($request->only('password'), [
                'password' => 'required|string|min:7|max:30',
            ]);
            if ($validator->fails()) {
                $code = response()->json($generalTrait->returnCodeAccordingToInput($validator));
                return response()->json($generalTrait->returnValidationError($validator,$code));
            }
        } catch (\Exception $e) {
            return response()->json($generalTrait->returnError($e->getCode(), $e->getMessage()));
        }
        $user = User::where('email',$request->email)->get()->first();
        if(!$user->is_verified){
            return response()->json($generalTrait->returnError('403', 'verify your account to reset YourPassword'));
        }
        else 
        return response()->json($generalTrait->returnError('401', 'you have to verify your email'));
        $user->password = bcrypt($request->password);
        $user->save();
        if($user)
            return response()->json($generalTrait->returnSuccessMessage('reset password is done successfully'));
        else 
            return response()->json($generalTrait->returnError('401', 'can\'t reset password'));
    }

    public function confirmCode(Request $request)
    {
        $generalTrait = new GeneralTrait;
        $user = User::where('email',$request->email)->get()->first();
        if($user->confirm_code == $request->confirmCode){
            $user->is_verified = true;
            //check
            $user->email_verified_at = time();
            $user->save();
            return response()->json($generalTrait->returnSuccessMessage('email verified successfully'));
        }
        return  response()->json($generalTrait->returnError('401', 'wrong code'));
    }
}
