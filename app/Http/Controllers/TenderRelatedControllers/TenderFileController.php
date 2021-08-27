<?php

namespace App\Http\Controllers\TenderRelatedControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Http\Controllers\MyValidator;
use App\Models\TenderRelated\Tender_file;

use Illuminate\Http\Request;


use function PHPUnit\Framework\returnSelf;

class TenderFileController extends Controller
{
    // store tender files in database 
    public static function store(Request $request)
    {
        $res = MyValidator::validation($request->only('file','tender_id','fileType'),[
            'file.*'=>'file|required',
            'tender_id'=>'required|exists:tenders,tender_id',
            'fileType' => 'required|in:financial requirement,technician requirement,other'

        ]);
        if(!$res['status']){
            return $res;
        }
        return FileController::storeFiles($request,'tender');
    }
    
    public function index(Request $request){
        $generalTrait = new GeneralTrait;
        if($request->type){
            $tenderFiles= Tender_file::index($request->tender_id)->where('type','=',$request->type)
            ->get();  
        }
        else{
            $tenderFiles= Tender_file::index($request->tender_id)->addSelect('files.file_id')
            ->get();
        }
        $tenderFiles = FileController::decryptCollection($tenderFiles);
        return $generalTrait->returnData('tenderFiles',$tenderFiles);
        
    }
    /*ublic function destroy(Request $request)
    {
       // request has file_id
       $generalTrait =  new GeneralTrait;
       $res = FileController::destroy($request->file_id,'tender');
       if($res === true){
        return $generalTrait->returnSuccessMessage("file deleted successfully");
    }
    else return $res;
    }*/

}
