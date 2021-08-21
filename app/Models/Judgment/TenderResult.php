<?php

namespace App\Models\Judgment;

use App\Models\TenderRelated\Tender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_result_id',
        'submit_form_id',
        'committee_member_id',
        'tender_id',
    ];
       
    protected $primaryKey = 'committee_judgment_id';

    protected $hidden = [
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function Submit_form(){
        return $this->belongsTo(Submit_form::class,'submit_form_id');
    }
    public function CommitteeMember(){
        return $this->belongsTo(CommitteeMember::class,'committee_member_id');
    }
    public function Tender(){
        return $this->belongsTo(Tender::class,'tender_id');
    }

}
