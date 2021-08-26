<?php

namespace App\Http\Controllers\TenderRelatedControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Models\TenderRelated\Tender_file;
use Illuminate\Http\Request;
use NunoMaduro\Collision\Adapters\Phpunit\State;
use PhpParser\Builder\Function_;

use function PHPUnit\Framework\returnSelf;

class TenderFileController extends Controller
{
    // store tender files in database 
    public static function store(Request $request)
    {
        $generalTrait =  new GeneralTrait;
        $res = FileController::storeFiles($request,'tender');
        // maybe happened a problem while comparing json with boolean 
        if($res === true){
            return $generalTrait->returnSuccessMessage("files uploaded successfully");
        }
        else return $res;
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
    public function destroy(Request $request)
    {
       // request has file_id
       $generalTrait =  new GeneralTrait;
       $res = FileController::destroy($request->file_id,'tender');
       if($res === true){
        return $generalTrait->returnSuccessMessage("file deleted successfully");
    }
    else return $res;
    }

}
