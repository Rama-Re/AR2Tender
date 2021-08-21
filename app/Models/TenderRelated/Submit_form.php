<?php

namespace App\Models\TenderRelated;

use App\Models\Judgment\JudgmentOfCommittee;
use App\Models\Judgment\TenderResult;
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

    public function JudgmentOfCommittee(){
        return $this->hasMany(JudgmentOfCommittee::class,'submit_form_id');
    }
    public function TenderResult(){
        return $this->hasOne(TenderResult::class,'submit_form_id');
    }
}
