<?php
namespace App\Http\Controllers\TenderRelatedControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Models\TenderRelated\Supplier_file;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Return_;

class SupplierFileController extends Controller
{
    //
    public function store(Request $request)
    {
        $generaltrait = new GeneralTrait;
        FileController::storeFiles($request,'supplier');
        return $generaltrait->returnSuccessMessage("files stored successfully");
    }
    public function index(Request $request){
        $generaltrait = new GeneralTrait;
        if($request->type){
            $filesFromDB = Supplier_file::index($request->submission_id)->where('type','=',$request->type)->get(); 
        }
        else{
            $filesFromDB = Supplier_file::index($request->submission_id)->get();
        }
        
        if($filesFromDB->isEmpty()){
            return $generaltrait->returnError('404',"there is no files or the submission is not exist");
        }
        $filesFromDB = FileController::decryptCollection($filesFromDB);
        return $generaltrait->returnData('files',$filesFromDB);
        
        
    }

}
