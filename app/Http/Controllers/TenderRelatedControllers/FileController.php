<?php

namespace App\Http\Controllers\TenderRelatedControllers;

use App\Helpers\NumberHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Http\Controllers\MyValidator;
use App\Models\TenderRelated\File;
use App\Models\TenderRelated\Supplier_file;
use App\Models\TenderRelated\Tender_file;
use Exception;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public static function replace_extension($filename, $new_extension) {
        $info = pathinfo($filename);
        return $info['dirname'].$info['filename'] . '.' . $new_extension;
    }
    public static function toUploadedFile($fileFromDB)
    {
        $filesystem = new Filesystem;
        $path = Crypt::decryptString($fileFromDB->path);
        $name = $filesystem->name($path);
        $extension = $filesystem->extension($path);
        $originalName = $name . '.' . $extension;
        $mimeType = $fileFromDB->mime_type;
        $error = null;
        
        $contents = Storage::get($path);
        $FileContent = Crypt::decryptString($contents);
        Storage::disk('local')->put($path, $FileContent);

        $new_path = self::replace_extension( $path,$fileFromDB->extension);

        dd(rename ($path,$new_path));

        return new UploadedFile($path, $originalName, $mimeType, $error, false);

    }

    public static function decryptCollection($files)
    {
        $decrypted = $files->map(function ($item, $key) {
            return [
                'file_id' => $item->file_id,
                'name' => substr(Crypt::decryptString($item->name), strpos(Crypt::decryptString($item->name), '%') + 1),
                'size' => $item->size,
                'path' => public_path(Crypt::decryptString($item->path)),
                'type' => $item->type,
            ];
        });
        return $decrypted->all();
    }
    //
    public function oneindex(Request $request)
    {
        #### not over need to bring the file_id from the database +   ####
        //give the name of the file and get the file
        //belongsto = 'tender', 'supplier'
        $res = MyValidator::validation($request->only('file_id', 'needPath'), [
            'file_id' => 'required|exists:files,file_id',
            'needPath' => 'boolean|required',
        ]);
        if (!$res['status']) {
            return $res;
        }
        $fileFromDB = File::where('file_id', $request->file_id)->get()->first();

        $path = Crypt::decryptString($fileFromDB->path);
        $needPath = $request->needPath;

        if ($needPath) {
            return GeneralTrait::returnData('path', $path);
        }
        try {
            $decRes = $this->decryptFile($fileFromDB);
            if ($decRes == true) {
                $file = response()->download(public_path($path));
                $hashRes = $this->hashFile(public_path($path));
                if ($hashRes == true) {
                    return $file;
                } else {
                    return GeneralTrait::returnError('403', 'could not get the file');
                }
            }
            return GeneralTrait::returnError('404',"something went wrong");
        }catch(Exception $e){
            return GeneralTrait::returnError('404',"som thing happened ".$e->getMessage());
            //return GeneralTrait::returnError('404',"couldn't open the file");

        }
    }

    public static function resFiles($resFiles)
    {
        //this function return an error message that has file names that aren't accepted
        $result = true;
        $generalTrait = new GeneralTrait;

        foreach ($resFiles as $resFile) {
            if ($resFile == false) {
                $result = false;
            }
        }
        if ($result == true) {
            return $generalTrait->returnData('files_id', $resFiles, "all files uploaded successfully");
        } else {
            return $generalTrait->returnData('files_id', $resFiles, "there is files did not uploaded");
        }
    }

    public static function storeFiles(Request $request, $belongsto)
    {
        $generalTrait = new GeneralTrait;
        $filesres = array();
        if ($request->hasFile('file')) {
            $filesId = self::store($request, $belongsto); //array of files id and names of files that couldn't be store
            foreach ($filesId as $fileId) {
                $fileToDB = null;
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
                        if ($result != true) {
                            //TODO ##### delete the file depending on the id ($fileId)####
                            //return $generalTrait->returnError('402', "error while saving,files belong to nowhere");4
                            array_push($filesres, false);
                        } else {
                            array_push($filesres, $fileId);
                        }
                    } else {
                        array_push($filesres, false); // push the name of the file to not accepted files
                    }
                } catch (Exception $e) {
                    array_push($filesres, false);
                    //$generalTrait->returnError('404', "the directory you are trying to reach is not exists");
                }
            }
            return FileController::resFiles($filesres);
        } else {
            return $generalTrait->returnError('403', "There isn't any file selected"); // required thing not passed
        }

    }

    public static function hashFile($filePath)
    {
        try {
            $handle = fopen($filePath, 'r');
            $oFileContent = fread($handle, $filePath->getSize());
            fclose($handle);
            $encryptedFileContent = Crypt::encryptString($oFileContent);
            $handle1 = fopen($filePath, 'w');
            fwrite($handle1, $encryptedFileContent);
            fclose($handle1);
            return true;
        } catch (Exception $e) {
            return false;
        }

    }
    public static function decryptFile($fileFromDB)
    {

        $filePath = self::toUploadedFile($fileFromDB);
        
        try {
            $handle = fopen($filePath, 'r');
            $oFileContent = fread($handle, $filePath->getSize());
            fclose($handle);
            $encryptedFileContent = Crypt::encryptString($oFileContent);
            $handle1 = fopen($filePath, 'w');
            fwrite($handle1, $encryptedFileContent);
            fclose($handle1);
            return true;
        } catch (Exception $e) {
            return false;
        }

    }

    public static function store(Request $request, $belongsto)
    {

        $files = array();

        foreach ($request->file as $file) {
            
            $res = self::hashFile($file);
            try {
                if ($res == true) {
                    $fileToDB = new File;
                    $fileToDB->mime_type = $file->getClientMimeType();
                    $fileToDB->extension = $file->getClientOriginalExtension();
                    $name = $file->getClientOriginalName();
                    $fileToDB->name = Crypt::encryptString(time() . '%' . $name); // I put the % to make it easy to get the name without the time
                    $fileToDB->type = $request->fileType; // 'financial requirement','technician requirement','other'
                    $fileToDB->belongsto = $belongsto; //'tender', 'supplier'
                    $fileToDB->path = Crypt::encryptString($path = $file->store('public/files/' . $belongsto)); // maybe I should hash here
                    $fileToDB->size = NumberHelper::readableSize(Storage::size($path)); //not woked
                    $fileToDB->save();
                    array_push($files, $fileToDB->file_id);
                } else {
                    array_push($files, false);
                }
            } catch (Exception $e) {
                array_push($files, false);
            }
        }
        return $files;

    }

    /* public static function destroy($file_id,$belongsto)
{
$generalTrait = new GeneralTrait();
if($belongsto == 'tender'){
$res =Tender_file::where('file_id',$file_id)->delete();

}else if($belongsto == 'supplier'){
$res =Supplier_file::where('file_id',$file_id)->delete();
}
if($res ==0){
return $generalTrait->returnError('400',"error happened the file could not deleted");
}

if (Storage::delete('public/files/' . $belongsto . $file)) {
return $generalTrait->returnSuccessMessage("the file " . $file . " is deleted");
} else {
return $generalTrait->returnError('404', "couldn't delete " . $file . " file");
}

}*/
}
