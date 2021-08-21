<?php

namespace App\Models\TenderRelated;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectiveCompany extends Model
{
    use HasFactory;
    protected $table = 'selective_companies';
    protected $primaryKey = 'selective_company_id';
    public $timestamps = false;
}
