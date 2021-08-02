<?php

namespace App\Models\TenderRelated;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submit_form extends Model
{
    use HasFactory;
    protected $table = 'submit_forms';
    protected $primaryKey = 'submit_form_id'; //the name is just neglecated
    public $incrementing = false;

    protected $attributes = [
        'price' => '0',
    ];

}
