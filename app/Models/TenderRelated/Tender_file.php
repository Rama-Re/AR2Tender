<?php

namespace App\Models\TenderRelated;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tender_file extends Model
{
    use HasFactory;
    protected $table = 'tender_files';
    protected $primaryKey = 'tender_file_id';

    protected $attributes = [
        'type' => 'other',
    ];
}
