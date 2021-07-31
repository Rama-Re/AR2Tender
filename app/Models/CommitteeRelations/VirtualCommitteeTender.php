<?php

namespace App\Models\CommitteeRelations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualCommitteeTender extends Model
{
    use HasFactory;
    protected $fillable = [
        'virtual_committee_tender_id',
        'virtual_committee_id',
        'tender_id',
        //what the meaning of task + add_date
        'task',
        'add_date'
    ];
    //primaryKey is committee_id with employee_id
    protected $primaryKey = 'virtual_committee_tender_id';
    
    public function Tender(){
        return $this->belongsTo(Tender::class,'tender_id');
    }
    public function VirtualCommittee(){
        return $this->belongsTo(VirtualCommittee::class,'virtual_committee_id');
    }
    public function VirtualCommitteeMember(){
        return $this->hasMany(VirtualCommitteeMember::class,'virtual_committee_member_id');
    }
}
