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
        return $query->select('supplier_files.file_id','name','size','path','type')
        ->join('files', 'files.file_id', '=', 'supplier_files.file_id')
        ->where('submit_form_id','=',$submitId);
    }
}
