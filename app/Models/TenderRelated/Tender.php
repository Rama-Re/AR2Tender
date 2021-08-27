<?php

namespace App\Models\TenderRelated;

use App\Http\Controllers\TenderRelatedControllers\SubmitFormController;
use App\Models\Account\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    use HasFactory;
    protected $table = 'tenders';
    protected $primaryKey = 'tender_id';
    protected $fillable = ['company_id','title','description','active','type','selective','category'];
    public $timestamps = false;

    protected $attributes = [
        'type' => 'Open',
        'category' => 'Other',
    ];
    
    public function TenderResult(){
        return $this->hasOne(TenderResult::class,'tender_id');
    }


    public function scopeIndex($query)
    {
        return $query->select('tenders.tender_id', 'end_date', 'title', 'company_name', 'image','image_path')
        ->join('tender_track', 'tender_track.tender_id', '=', 'tenders.tender_id')
        ->join('companies', 'tenders.company_id', '=', 'companies.company_id');

    }
    public function scopePublic($query)
    {
        return $query->whereNull('tenders.selective')
        ->orWhere('tenders.selective', '!=', 'companies')
        ->where('tenders.active','=',true);

    }
    public function scopeActive($query){
        return $query->where('tenders.active','=',true);
    }

    public function SelectiveCompany(){
        return $this->hasMany(SelectiveCompany::class,'tender_id');
    }
    public function SelectiveCountry(){
        return $this->hasMany(SelectiveCountry::class,'tender_id');
    }
    public function Submit_form(){
        return $this->hasMany(Submit_form::class,'tender_id');
    }
    public function Tender_track(){
        return $this->hasOne(Tender_track::class,'tender_id');
    }
    public function SelectiveSpecialty(){
        return $this->hasOne(SelectiveSpecialty::class,'tender_id');
    }
    public function Tender_file(){
        return $this->hasMany(Tender_file::class,'tender_id');
    }
    
    public function Company(){
        return $this->belongsTo(Company::class,'company_id');
    }




}
