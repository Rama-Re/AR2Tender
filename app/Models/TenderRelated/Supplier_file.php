<?php

namespace App\Models\TenderRelated;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier_file extends Model
{
    use HasFactory;
    protected $table = 'supplier_files';
    protected $primaryKey = 'supplier_file_id';
}
