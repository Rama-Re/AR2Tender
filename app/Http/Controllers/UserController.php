<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AccountControllers;
use Illuminate\support\Facades\Hash;
use App\Models\User;
use App\Models\Account\Company;
use App\Models\Account\Employee;

class UserController extends Controller
{
    function create($request){
        
        $request->validate([
            'email'=>'required|email|unique:users',
            'password'=>'required|min:7|max:16',
            'type'=>'required'
        ]);
        $User = new User;
        if ($request->type == 'company'){
            $compresponce = CompanyController::create($request);
            if($compresponce->Result == 'success'){
                $User->company_id = $compresponce->company_id;
                $User->employee_id = null;
            }
            else return $compresponce;
        }
        else if($request->type == 'employee'){
            $empresponce = CompanyController::create($request);
            if($empresponce->Result == 'success'){
                $User->employee_id = $empresponce->employee_id;
                $User->company_id = null;
            }
            else return $empresponce;
        }
        $User->email = $request->email;
        $User->password = $request->password;
        $User->type = $request->type;
        //I have to connect it with the company or the employee
        $query = $User->save();
        if($query)
        {
            return ['Result'=>'success','message' => 'You have been successfuly registered'];
        }else{
            return ['Result'=>'fail','message' => 'Something went wrong'];
        }
    }
    function check(Request $request){
        $request->validate([
            'email'=>'required|email',
            'password'=>'required|min:7|max:16'
        ]);

        $User = User::where('email','=',$request->email)->first();
        if($User){
            if(Hash::check($request->password, $User->password)){
                //return CompanyAccount or EmployeeAccount
                //I have to check itttttttt
                if($User->type == 'Company'){
                    $Company = Company::find($User->company_id);
                    return $responce->json($Company);
                }else{
                    $Employee = Employee::find($User->employee_id);
                    return $responce->json($Employee);
                }
            }else{
                return ['Result'=>'fail','message' => 'Invalid password'];
            }
        }else{
            return ['Result'=>'fail','message' => 'No account found for this email'];
        }
    }
    
}
