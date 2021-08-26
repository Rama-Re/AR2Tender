<?php

use App\Http\Controllers\AccountControllers\AdminController;
use App\Http\Controllers\AccountControllers\UserAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountControllers\CompanyController;
use App\Http\Controllers\AccountControllers\EmployeeController;
use App\Http\Controllers\AccountControllers\FCMTokenController;
use App\Http\Controllers\AccountControllers\UserController;
use App\Http\Controllers\LocWithConnectControllers\CityController;
use App\Http\Controllers\LocWithConnectControllers\CountryController;
use App\Http\Controllers\LocWithConnectControllers\LocationController;
use App\Http\Controllers\TenderRelatedControllers\FileController;
use App\Http\Controllers\TenderRelatedControllers\SupplierFileController;
use App\Http\Controllers\TenderRelatedControllers\TenderController;
use App\Http\Controllers\TenderRelatedControllers\TenderFileController;
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
Route::get("company/getProfile",[CompanyController::class,'getProfile']);

Route::post("admin/register", [AdminController::class,'register']);

Route::group(['middleware' => ['checkToken','active_user','verifyUser']], function () {
    
    Route::post("fcm/saveFCMToken", [FCMTokenController::class,'saveFCMToken']);

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
        //Route::get("company/getProfile",[CompanyController::class,'getProfile']);
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

    Route::post("/",[TenderController::class,'store']);
    Route::get("filter",[TenderController::class,'filter']);
    Route::get("indexSearch",[TenderController::class,'indexSearch']);
    Route::get("indexMyTenders",[TenderController::class,'indexMyTenders']);
    Route::get("indexSubmittedTo",[TenderController::class,'indexSubmittedTo']);
    Route::put("/{tender}",[TenderController::class,'update'])->whereNumber('tender'); // in the web this make a problem so we put inside the form @method('PUT')
    
    
});
Route::group(['prefix' => 'tender'], function () {

    //maybe I will change the route to be like this.. and remove the id from the body
    //Route::post("storefiles/{tender_id}",[TenderFileController::class,'store'])->whereNumber('tender_id');
    Route::post("storefiles",[TenderFileController::class,'store']);
    Route::get("indexfiles",[TenderFileController::class,'index']);
    
});
Route::group(['prefix' => 'supplier'], function () {

    //maybe I will change the route to be like this.. and remove the id from the body
    //Route::post("storefiles/{submit_form_id}",[SupplierFileController::class,'store'])->whereNumber('submit_form_id');
    Route::post("storefiles",[SupplierFileController::class,'store']);
    Route::get("indexfiles",[SupplierFileController::class,'index']);
    
    
});
Route::post("getUser",[UserAuthController::class,'getUser']);
Route::get("index/{directory}",[FileController::class,'oneindex']);

Route::get("emailsFromTender",[TenderController::class,'emailsFromTender']);

Route::get("getLocations",[LocationController::class,'index']);
Route::get("getCountries",[CountryController::class,'index']);

