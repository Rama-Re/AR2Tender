<?php

namespace App\Models\Judgment;

use App\Models\CommitteeRelations\CommitteeMember;
use App\Models\TenderRelated\Submit_form;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JudgmentOfCommittee extends Model
{
    use HasFactory;

    protected $fillable = [
        'committee_judgment_id',
        'submit_form_id',
        'committee_member_id',
        'judgment',
        'vote'
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

}
