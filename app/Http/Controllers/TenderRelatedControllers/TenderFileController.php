<?php

namespace App\Http\Controllers\TenderRelatedControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Models\TenderRelated\Tender_file;
use Illuminate\Http\Request;

class TenderFileController extends Controller
{
    // store tender files in database 
    public function store(Request $request)
    {
        $generalTrait =  new GeneralTrait;
        FileController::storeFiles($request,'tender');
        return $generalTrait->returnSuccessMessage("files uploaded successfully");
    }
    
    public function index(Request $request){
        $generalTrait = new GeneralTrait;
        if($request->type){
            $tenderFiles= Tender_file::index($request->tender_id)->where('type','=',$request->type)->get();  
        }
        else{
            $tenderFiles= Tender_file::index($request->tender_id)->get();
        }
        $tenderFiles = FileController::decryptCollection($tenderFiles);
        return $generalTrait->returnData('tenderFiles',$tenderFiles);
        
    }

}
