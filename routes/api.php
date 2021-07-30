<?php

use App\Http\Controllers\AccountControllers\AdminController;
use App\Http\Controllers\AccountControllers\UserAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountControllers\CompanyController;
use App\Http\Controllers\AccountControllers\EmployeeController;
use App\Http\Controllers\AccountControllers\UserController;
use App\Http\Controllers\LocWithConnectControllers\CityController;
use App\Http\Controllers\LocWithConnectControllers\CountryController;
use App\Http\Controllers\LocWithConnectControllers\LocationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::post("saveCountries", [CountryController::class,'save']);
Route::post("saveCities", [LocationController::class,'save']);
Route::post("company/upload", [CompanyController::class,'uploadCompanyPhoto']);
Route::post("company/register", [CompanyController::class,'register']);
Route::get("user/login", [UserAuthController::class,'login']);

///***///
//Admin Group
///***///
Route::group(['middleware' => ['checkToken','checkType:admin','json.response']], function () {
    Route::post("admin/register", [AdminController::class,'register']);
    Route::get("admin/getProfile",[AdminController::class,'getProfile']);
});

///***///
//Company Group
///***///
Route::group(['middleware' => ['checkToken','checkType:company']], function () {
    Route::post("employee/register", [EmployeeController::class,'register']);
    Route::get("company/getProfile",[CompanyController::class,'getProfile']);
});

///***///
//Employee Group
///***///
Route::group(['middleware' => ['checkToken','checkType:employee']], function () {
    Route::get("employee/getProfile",[EmployeeController::class,'getProfile']);
});
