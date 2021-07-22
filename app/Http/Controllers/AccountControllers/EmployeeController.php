<?php

namespace App\Http\Controllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Account\Employee;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    use GeneralTrait;
    public static function validation(Request $request){
        try {
            $data = $request->only('employee_name','company_id');
            $validator = Validator::make($data, [
                'employee_name' => 'required|string',
                'company_id'=> 'required'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                $code = GeneralTrait::returnCodeAccordingToInput($validator);
                return GeneralTrait::returnValidationError($code, $validator);
            }
            else return GeneralTrait::returnSuccessMessage('validated');
        } catch (\Exception $e) {
            return GeneralTrait::returnError($e->getCode(), $e->getMessage());
        }
    }
    public function register(Request $request){
        $result = UserAuthController::validation($request);
        if($result->status){
            $result2 = $this->validation($request);
            if($result2->status){
                //Request is valid, create new user
                $response = (new UserAuthController)->register($request);
                $employee = Employee::create([
                    'employee_name' => $request->company_name,
                    'company_id' => $request ->company_id,
                    'user_id' => $response->user->user_id,
                ]);
                //Admin created, return success response
                return $this->returnSuccessMessage('Employee created successfully');
            }
            else return $result2;
        }
        else return $result;
    }
    public function getProfile(Request $request){
        $response = UserAuthController::validationToken($request);
        if($response->status){
            $result = UserAuthController::getUser($request);
            $employee = Employee::where('user_id',$result->user->user_id)->get();
            if (!$employee) {
                return $this->returnError('001', 'this admin is not found');
            }
            return $this->returnData('employee', $employee);
        }
        return $response;
    }
}
