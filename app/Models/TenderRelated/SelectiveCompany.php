<?php

namespace App\Models\TenderRelated;

use App\Models\Account\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectiveCompany extends Model
{
    use HasFactory;
    protected $table = 'selective_companies';
    protected $primaryKey = 'selective_company_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = ['company_id','tender_id'];

    public function scopeName($query,$id)
    {
        return $query->select('companies.company_id','companies.company_name')
        ->join('companies', 'companies.company_id', '=', 'selective_companies.company_id');

    }
    public function Company(){
        return $this->belongsTo(Company::class,'company_id');
    }
    public function Tender(){
        return $this->belongsTo(Tender::class,'tender_id');
    }
   
}
