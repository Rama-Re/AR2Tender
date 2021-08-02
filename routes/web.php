<?php

use App\Http\Controllers\AccountControllers\AdminController;
use App\Mail\SampleMail;
use App\Models\TenderRelated\Tender;
use App\Models\TenderRelated\Tender_track;
use Carbon\Carbon;
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

Route::get("/", function(){

    $date ='end_date';
    $still = false;
    $tendersfromDB = Tender::select('tenders.tender_id',$date,'Title','company_name','image')
            ->join('tender_track', 'tender_track.tender_id', '=', 'tenders.tender_id')
            ->join('companies','tenders.company_id','=','companies.company_id')
            ->where($date,$still?'>=':'<=',Carbon::now())->latest($date)
            ->get();
    dd($tendersfromDB);
    
});


//Route::get("/", function(){return new SampleMail();});
Route::get("register", [AdminController::class,'register']);