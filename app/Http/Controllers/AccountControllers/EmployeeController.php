<?php

namespace App\Http\Controllers\AccountControllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class EmployeeController extends Controller
{
    function create(Request $request){
        $Employee = new Employee;
        $Employee->employee_name = $request->employee_name;
        $query = $Employee->save();
        if($query){
            return ['Result'=>'success','employee_id' => $Employee->id];
        }else{
            return ['Result'=>'failed','message' => 'something wrong in employee name'];
        }
    }
    
}
