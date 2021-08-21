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
        return $query->select('name','size','path','type')
        ->join('files', 'files.file_id', '=', 'tender_files.file_id')
        ->where('tender_id','=',$tender_id);
    }
}
