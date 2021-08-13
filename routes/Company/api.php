<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountControllers\CompanyController;
use App\Http\Controllers\AccountControllers\EmployeeController;

///***///
//Company Group
///***///
Route::group(['middleware' => ['checkToken','checkType:company']], function () {
    Route::post("employee/register", [EmployeeController::class,'register']);
    Route::get("company/getProfile",[CompanyController::class,'getProfile']);
    Route::get("company/changeStatus",[CompanyController::class,'changeStatus']);
});
