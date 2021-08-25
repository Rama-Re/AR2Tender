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

    $generalTrait= new GeneralTrait;
    $still = false;
    $tendersfromDB = Country::all();
    $str = 'public/files/tender/MkJJl55mnD6333KIJB44VVV445jnjnFSllmD5548sHYH.pdf';
    $notAcceptedFiles = array();
    array_push($notAcceptedFiles, "etClientOriginalName(1)");
    array_push($notAcceptedFiles, "etClientOriginalName(2)");

    if (!empty($notAcceptedFiles)) {
        if (sizeof($notAcceptedFiles) > 1) {
            $mes = "only pdf files are allowed so those files are not accepted(";
            foreach ($notAcceptedFiles as $notAccepted) {
                if ($notAccepted == end($notAcceptedFiles)) {
                    $mes .= $notAccepted.')' ;
                }
                else{
                    $mes .= $notAccepted . ", ";
                }
            }
        }else{
            $mes = "only pdf files are allowed so ".array_values($notAcceptedFiles)[0]." file is not accepted";
        }
        return $generalTrait->returnError('402', $mes);  
    } else {
        return $generalTrait->returnSuccessMessage("uploaded successfully");
    }
    //dd(Storage::files('storage\app\public'));
    //dd(StringHelperFunctions::between_last('/','.pdf',$str));

});

