<?php

use App\Http\Controllers\AccountControllers\AdminController;
use App\Mail\SampleMail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get("/", function(){return new SampleMail();});
Route::get("register", [AdminController::class,'register']);
