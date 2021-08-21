<?php

namespace App\Models\TenderRelated;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    use HasFactory;
    protected $table = 'tenders';
    protected $primaryKey = 'tender_id';
    public $timestamps = false;

    protected $attributes = [
        'type' => 'Open',
        'category' => 'Other',
    ];
    
    public function TenderResult(){
        return $this->hasOne(TenderResult::class,'tender_id');
    }
}
