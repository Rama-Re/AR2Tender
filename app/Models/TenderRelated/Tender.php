<?php

namespace App\Models\TenderRelated;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    use HasFactory;
    protected $table = 'tenders';
    protected $primaryKey = 'tender_id';
    protected $fillable = ['title','description','active','type','selective','category'];
    public $timestamps = false;

    protected $attributes = [
        'type' => 'Open',
        'category' => 'Other',
    ];

    public function scopeIndex($query)
    {
        return $query->select('tenders.tender_id', 'end_date', 'Title', 'company_name', 'image','image_path')
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
    

}
