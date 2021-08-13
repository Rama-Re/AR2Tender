<?php

namespace App\Models\CommitteeRelations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualCommitteeMember extends Model
{
    use HasFactory;
    protected $fillable = [
        'virtual_committee_member_id',
        'virtual_committee_id',
        'employee_id',
        'task',
    ];
    //primaryKey is committee_id with employee_id
    //protected $primaryKey = 'virtual_committee_member_id';
    
    public function Employee(){
        return $this->belongsTo(Employee::class,'employee_id');
    }
    public function VirtualCommittee(){
        return $this->belongsTo(VirtualCommittee::class,'virtual_committee_id');
    }
    public function Judgment(){
        return $this->hasMany(Judgment::class,'virtual_committee_member_id');
    }
}
