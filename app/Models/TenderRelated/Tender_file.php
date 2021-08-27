<?php

namespace App\Models\TenderRelated;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tender_file extends Model
{
    use HasFactory;
    protected $table = 'tender_files';
    protected $primaryKey = 'tender_file_id';


    public function scopeIndex($query,$tender_id)
    {
        return $query->select('files.file_id','name','size','path','type')
        ->join('files', 'files.file_id', '=', 'tender_files.file_id')
        ->where('tender_id','=',$tender_id); 
    }
    public function File(){
        return $this->belongsTo(File::class,'file_id');
    }
    public function Tender(){
        return $this->belongsTo(Tender::class,'tender_id');
    }
}
