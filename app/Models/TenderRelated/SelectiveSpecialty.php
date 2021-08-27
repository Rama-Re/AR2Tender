<?php

namespace App\Models\TenderRelated;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectiveSpecialty extends Model
{
    protected $fillable = ['company_id','tender_id'];
    public $timestamps = false;
    public $table ='selective_specialty';
    public $incrementing = false;
    use HasFactory;
    
    public function Tender(){
        return $this->belongsTo(Tender::class,'tender_id');
    }
}
