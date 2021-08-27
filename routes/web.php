<?php

use App\Http\Controllers\AccountControllers\AdminController;
use App\Mail\SampleMail;
use App\Models\LocationWithConnect\Country;
use App\Models\TenderRelated\Tender;
use App\Models\TenderRelated\Tender_track;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use App\Helpers\StringHelperFunctions;
use App\Http\Controllers\GeneralTrait;
use Illuminate\Support\Facades\Storage;
use Andyabih\LaravelToUML\Http\Controllers\LaravelToUMLController;

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
