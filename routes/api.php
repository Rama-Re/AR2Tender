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

Route::post("saveCountries", [CountryController::class,'save']);
Route::post("saveCities", [LocationController::class,'save']);

Route::get("company/getCompanyById", [CompanyController::class,'getCompanyById']);
Route::post("company/upload", [CompanyController::class,'uploadCompanyPhoto']);
Route::post("company/register", [CompanyController::class,'register']);
Route::post("user/login", [UserAuthController::class,'login']);

///***///
//Admin Group
///***///


Route::post("admin/register", [AdminController::class,'register']);

Route::group(['middleware' => ['checkToken','checkType:admin','json.response']], function () {
   // Route::post("admin/register", [AdminController::class,'register']);
    Route::get("admin/getProfile",[AdminController::class,'getProfile']);
    Route::get("company/getAll",[CompanyController::class,'getall']);
});

///***///
//Company Group
///***///
Route::group(['middleware' => ['checkToken','checkType:company']], function () {
    Route::post("employee/register", [EmployeeController::class,'register']);
    Route::get("company/getProfile",[CompanyController::class,'getProfile']);
    Route::get("company/changeStatus",[CompanyController::class,'changeStatus']);
});

///***///
//Employee Group
///***///
Route::group(['middleware' => ['checkToken','checkType:employee']], function () {
    Route::get("employee/getProfile",[EmployeeController::class,'getProfile']);
});

Route::group(['prefix' => 'tenders'], function () {

    Route::post("/",[TenderController::class,'store']);
    Route::get("filter",[TenderController::class,'filter']);
    Route::get("indexSearch",[TenderController::class,'indexSearch']);
    Route::get("indexMyTenders",[TenderController::class,'indexMyTenders']);
    Route::get("indexSubmittedTo",[TenderController::class,'indexSubmittedTo']);
    
    
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


