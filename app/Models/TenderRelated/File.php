<?php

namespace App\Models\TenderRelated;

use App\Http\Controllers\TenderRelatedControllers\SupplierFileController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'files';
    protected $primaryKey = 'file_id';

    protected $attributes = [
        'type' => 'other',
    ];
    use HasFactory;
    public function Tender_file(){
        return $this->hasOne(Tender_file::class,'file_id');
    }
    public function Supplier_file(){
        return $this->hasOne(Supplier_file::class,'file_id');
    }
}
