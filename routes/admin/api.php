<?php

use App\Http\Controllers\AccountControllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountControllers\CompanyController;

///***///
//Admin Group
///***///

Route::group(['middleware' => ['checkToken','checkType:admin','json.response']], function () {
    Route::post("admin/register", [AdminController::class,'register']);
    Route::get("admin/getProfile",[AdminController::class,'getProfile']);
    Route::get("company/getAll",[CompanyController::class,'getall']);
});
