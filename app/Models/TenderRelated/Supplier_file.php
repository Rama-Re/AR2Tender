<?php

namespace App\Models\TenderRelated;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier_file extends Model
{
    use HasFactory;
    protected $table = 'supplier_files';
    protected $primaryKey = 'supplier_file_id';

    public function scopeIndex($query,$submitId)
    {
        return $query->select('files.file_id','name','size','path','type')
        ->join('files', 'files.file_id', '=', 'supplier_files.file_id')
        ->where('submit_form_id','=',$submitId);
    }
    public function File(){
        return $this->belongsTo(File::class,'file_id');
    }
    public function Submit_form(){
        return $this->belongsTo(Submit_form::class,'submit_form_id');
    }
}
