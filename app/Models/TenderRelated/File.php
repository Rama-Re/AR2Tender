<?php

namespace App\Models\TenderRelated;

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

}
