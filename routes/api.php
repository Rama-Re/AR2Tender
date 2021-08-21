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
use App\Http\Controllers\TenderRelatedControllers\TenderController;

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


//Route::get("company/getCompanyById", [CompanyController::class,'getCompanyById']);
Route::post("company/register", [CompanyController::class,'register']);
Route::post("company/upload", [CompanyController::class,'uploadCompanyPhoto']);

Route::post("user/verifyAccount", [UserAuthController::class,'verifyAccount']);
Route::post("user/confirmCode", [UserAuthController::class,'confirmCode']);
Route::post("user/forgetPassword", [UserAuthController::class,'forgetPassword']);
Route::post("user/resetPassword", [UserAuthController::class,'resetPassword']);

Route::group(['middleware' => ['verifyUser','active_user']], function () {
    Route::post("user/login", [UserAuthController::class,'login']);
    Route::post("user/logout", [UserAuthController::class,'logout']);
});

Route::post("admin/register", [AdminController::class,'register']);

Route::group(['middleware' => ['checkToken','active_user','verifyUser']], function () {
    ///***///
    //Admin Group
    ///***///
    Route::group(['middleware' => ['checkType:admin']], function () {
        Route::post("saveCountries", [CountryController::class,'save']);
        Route::post("saveCities", [LocationController::class,'save']);
        Route::get("admin/getProfile",[AdminController::class,'getProfile']);
        Route::get("getAllCompanies",[CompanyController::class,'getall']);
        Route::get("user/bloackUser",[UserAuthController::class,'bloackUser']);
        Route::get("user/unbloackUser",[UserAuthController::class,'unbloackUser']);
    });
    ///***///
    //Company Group
    ///***///
    Route::group(['middleware' => ['checkType:company']], function () {
        Route::post("employee/register", [EmployeeController::class,'register']);
        Route::get("company/getProfile",[CompanyController::class,'getProfile']);
        Route::get("company/changeStatus",[CompanyController::class,'changeStatus']);
        Route::delete("employee/destroyUser",[EmployeeController::class,'destroyUser']);
        Route::post("employee/sentEmailToRegister",[EmployeeController::class,'sentEmailToRegister']);
    });
    ///***///
    //Employee Group
    ///***///
    Route::group(['middleware' => ['checkType:employee']], function () {
        Route::get("employee/getProfile",[EmployeeController::class,'getProfile']);
    });

});



Route::group(['prefix' => 'tenders'], function () {

    Route::group(['prefix' => 'filter'], function () {
        Route::post("category", [TenderController::class,'indexFilterOnCategory']);
        Route::post("date", [TenderController::class,'indexFilterOnDate']);
    });

});
