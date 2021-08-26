<?php

namespace App\Http\Controllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Http\Controllers\MyValidator;
use App\Models\Account\Employee;
use App\Models\Account\Company;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public static function validation(Request $request){
        return MyValidator::validation($request->only('employee_name','company_id'), [
            'employee_name' => 'required|string',
            'company_id'=> 'required'
        ]);
    }
    public function register(Request $request){
        $generalTrait = new GeneralTrait;
        $userC = new UserAuthController;
        $result = UserAuthController::validation($request);
        if($result["status"]){
            $result2 = $this->validation($request);
            if($result2["status"]){
                //Request is valid, create new user
                $response = $userC->register($request);
                $employee = new Employee;
                $employee->employee_name = $request->employee_name;
                $employee->company_id = $request->company_id;
                $employee->user_id = ($response["user"])->user_id;
                $user = User::find(($response["user"])->user_id);
                $user->is_verified = true;
                $user->save();
                //$employee->user_id = 1;
                $employee->save();
                
                //Employee created, return success response
                return $generalTrait->returnSuccessMessage('Employee created successfully');
            }
            else return $result2;
        }
        else return $result;
    }
    public function getProfile(Request $request){
        $generalTrait = new GeneralTrait;
        $response = UserAuthController::validationToken($request);
        if($response["status"]){
            $result = UserAuthController::getUser($request);
            if($result["status"]){
                $employee = Employee::where('user_id',$result["user"]->user_id)->get()->first();
                if (!$employee) {
                    return response()->json($generalTrait->returnError('404', 'Not Found'));
                }
                
                return response()->json($generalTrait->returnData('employee', $employee));
            }
        }
        return response()->json($response);
    }
    public function index(){
        $generalTrait = new GeneralTrait;
        $employees = Employee::get();
        return $generalTrait ->returnData('employees',$employees);
    }
    public function getCompanyById(Request $request){
        $generalTrait = new GeneralTrait;
        $employee = Employee::find($request->id);
        if (!$employee) {
            return $generalTrait->returnError('401', 'this employee is not found');
        }
        return $generalTrait->returnData('employee', $employee);
    }
    public function getAllCompanyEmployees(Request $request){
        $generalTrait = new GeneralTrait;
        $employees = Employee::where('company_id',$request->company_id)->get();
        if (!$employees) {
            return $generalTrait->returnError('401', 'there is\'t employees in this company');
        }
        return $generalTrait->returnData('employees', $employees);
    }
    public function sentEmailToRegister(Request $request)
    {
        $response = UserAuthController::validationToken($request);
        if($response["status"]){
            $result = UserAuthController::getUser($request);
            if($result["status"]){
                $company_name = Company::where('user_id',$result["user"]->user_id)->get('company_name')->first();
                $employee = Employee::join('users','users.user_id','=','employees.user_id')->where('employee_id',$request->employee_id)->get(['email','password'])->first();
                if(!$employee) return response()->json(GeneralTrait::returnError('404', 'this employee is not found'));
                $details = [
                    'title'=> $company_name.'\nYou have to sign in at AR2Tender Application with this account',
                    'body'=> 'email: '.$employee->email.'\npassword: '.$request->password
                ];
                Mail::to($request->email)->send(new \App\Mail\SampleMail($details));
                return response()->json(GeneralTrait::returnSuccessMessage('email send successfully'));
            }
            else return response()->json($result);
        }
        else return response()->json($response);
    }
    
    public function destroyUser(Request $request)
    {
        $user = User::where('email',$request->email)->get()->first();
        if($user) {
            $user->delete();
            return response()->json(GeneralTrait::returnSuccessMessage('Account deleted successfully'));
        }
        return response()->json(GeneralTrait::returnError('404', 'Not Found'));
    }
    public function editProfile(Request $request)
    {
        $user_id = UserAuthController::getUser($request)['user']->user_id;
        $company_id = Company::where('user_id',$user_id)->get('company_id')->first()->company_id;
        $employee = Employee::where('employee_id',$request->employee_id)->get()->first();
        if($company_id != $employee->company_id){return response()->json(GeneralTrait::returnError('403','this is not your employee'));}
        $employee->employee_name = $request->employee_name;
        $employee->user_id = $user_id;
        $employee->save();
        if($employee) return response()->json(GeneralTrait::returnData('employee',$employee));
        else return response()->json(GeneralTrait::returnError('404','something went wrong'));
    }
}
