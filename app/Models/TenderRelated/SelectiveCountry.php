<?php

namespace App\Models\TenderRelated;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectiveCountry extends Model
{
    protected $fillable = ['company_id','tender_id'];
    public $timestamps = false;
    public $incrementing = false;
    use HasFactory;
}
