<?php

namespace App\Models\TenderRelated;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectiveCountry extends Model
{
    protected $fillable = ['country_id','tender_id'];
    public $timestamps = false;
    public $incrementing = false;
    use HasFactory;

    public function scopeName($query,$id)
    {
        return $query->select('countries.country_id','countries.country_name')
        ->join('countries', 'countries.country_id', '=', 'selective_countries.country_id');

    }
    public function Country(){
        return $this->belongsTo(Country::class,'country_id');
    }
    public function Tender(){
        return $this->belongsTo(Tender::class,'tender_id');
    }
}
