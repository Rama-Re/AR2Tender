<?php

namespace App\Http\Controllers\TenderRelatedControllers;

use App\Helpers\NumberHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Models\TenderRelated\File;
use App\Models\TenderRelated\Supplier_file;
use App\Models\TenderRelated\Tender_file;
use Exception;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Lcobucci\JWT\Signer\Ecdsa\Sha512;
use PhpParser\Node\Stmt\Else_;

class FileController extends Controller
{
    public static function decryptCollection($files){
        $decrypted = $files->map(function($item,$key){
            return [
                'name' => Crypt::decryptString($item->name),
                'size'=> $item->size,
                'path' => Crypt::decryptString($item->path),
                'type' =>$item->type,
           ];
        });
        return $decrypted->all();
    }
    //
    public function oneindex(Request $request, $belongsto)
    {
        #### not over need to bring the file_id from the database +   ####
        //give the name of the file and get the file
        $fileId = $request->Id; // tenderId or SubmissionId
        //belongsto = 'tender', 'supplier'
        $fileName = $request->fileName;
        dd(hash_file('sha1', public_path('files/' . $belongsto . '/' . $fileName . '.pdf')));

        return response()->download(public_path('files/' . $belongsto . '/' . $fileName . '.pdf'));
    }

    public static function notAcceptedFiles($notAcceptedFiles)
    {
        //this function return an error message that has file names that aren't accepted
        $generalTrait = new GeneralTrait;
        if (!empty($notAcceptedFiles)) {
            if (sizeof($notAcceptedFiles) > 1) {
                $mes = "those files are not accepted(";
                foreach ($notAcceptedFiles as $key => $notAccepted) {
                    if ($key == (sizeof($notAcceptedFiles) - 1)) {
                        $mes .= $notAccepted . ')';
                    } else {
                        $mes .= $notAccepted . ", ";
                    }
                }
            } else {
                $mes =  array_values($notAcceptedFiles)[0] . " file is not accepted";
            }
            return $generalTrait->returnError('402', $mes);
        } else {
            return $generalTrait->returnSuccessMessage("uploaded successfully");
        }
    }

    public static function storeFiles(Request $request, $belongsto)
    {
        $generalTrait = new GeneralTrait;
        $notAccepted = array();
        if ($request->hasFile('file')) {
            $filesId = self::store($request, $belongsto); //array of files id and names of files that couldn't be store
            foreach ($filesId as $fileId) {
                if ($belongsto == 'tender') {
                    $fileToDB = new Tender_file;
                    $fileToDB->tender_id = $request->tender_id;
                } else if ($belongsto == 'supplier') {
                    $fileToDB = new Supplier_file;
                    $fileToDB->submit_form_id = $request->submit_form_id;
                }                
                try {
                    if (is_numeric($fileId)) {
                        $fileToDB->file_id = $fileId;
                        $result = $fileToDB->save();
                        if($result != true){
                           //TODO ##### delete the file depending on the id ($fileId)####
                            return $generalTrait->returnError('402',"error while saving,files belong to nowhere");
                        }
                    } else {
                        array_push($notAccepted, $fileId); // push the name of the file to not accepted files
                    }
                } catch (Exception $e) {
                    $generalTrait->returnError('404',"the directory you are trying to reach is not exists");
                }

            }
            return FileController::notAcceptedFiles($notAccepted);
        } else {
            return $generalTrait->returnError('403', "There isn't any file selected"); // required thing not passed
        }

    }

    public static function store(Request $request, $belongsto)
    {

        $files = array();
        
        foreach ($request->file as $file) {
           // if ($file->extension() == 'pdf') {
                $fileToDB = new File;
                $name = $file->getClientOriginalName();
                $fileToDB->name = Crypt::encryptString(time() . '%' . $name); // I put the % to make it easy to get the name without the time
        
                $fileToDB->type = $request->fileType; // 'financial requirement','technician requirement','other'
                $fileToDB->belongsto = $belongsto; //'tender', 'supplier'
                $fileToDB->path = Crypt::encryptString($path =$file->store('public/files/' . $belongsto)); // maybe I should hash here
                $fileToDB->size = NumberHelper::readableSize(Storage::size($path)); //not woked
                $fileToDB->save();
                array_push($files, $fileToDB->file_id);
           // } else {
            //    array_push($files, Crypt::encryptString($path =$file->store('public/files/' . $belongsto)));
            //}
        }
        
        return $files;

    }

    public function delete(Request $request)
    {
        /** not done it needs work */
        $generalTrait = new GeneralTrait();
        $file = $request->id;

        if (Storage::delete('public/files/' . $file->type . $file)) {
            return $generalTrait->returnSuccessMessage("the file " . $file . " is deleted");
        } else {
            return $generalTrait->returnError('404', "couldn't delete " . $file . " file");
        }

    }
}
