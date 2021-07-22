<?php

namespace App\Http\Controllers\AccountControllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AccountControllers\CompanyController;
use App\Http\Controllers\AccountControllers\EmployeeController;
use Illuminate\support\Facades\Hash;
use App\Models\User;
use App\Models\Account\Company;
use App\Models\Account\Employee;

class UserController extends Controller
{
    protected static function getTypeOfUserByEmail(Request $request){
        $user = User::where('email',$request->email)->get();
    }
    protected static function getTypeOfUserById(Request $request){
        $user = User::find($request->user_id);
    }
}
