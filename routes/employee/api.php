<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountControllers\EmployeeController;

///***///
//Employee Group
///***///
Route::group(['middleware' => ['checkToken','checkType:employee']], function () {
    Route::get("employee/getProfile",[EmployeeController::class,'getProfile']);
});
