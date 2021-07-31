<?php

namespace App\Http\Controllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Models\Account\Employee;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public static function validation(Request $request){
        $generalTrait = new GeneralTrait;
        try {
            $data = $request->only('employee_name','company_id');
            $validator = Validator::make($data, [
                'employee_name' => 'required|string',
                'company_id'=> 'required'
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
                $employee = Employee::where('user_id',$result["user"]->user_id)->get();
                if (!$employee) {
                    return $generalTrait->returnError('404', 'Not Found');
                }
                
                return $generalTrait->returnData('employee', $employee);
            }
        }
        return $response;
        $response = UserAuthController::validationToken($request);
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
}
