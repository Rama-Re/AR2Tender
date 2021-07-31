<?php

namespace App\Models\CommitteeRelations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualCommitteeMember extends Model
{
    use HasFactory;
    protected $fillable = [
        'virtual_committee_member_id',
        'virtual_committee_tender_id',
        'employee_id',
        //what the meaning of task + add_date
        'task',
        'add_date'
    ];
    //primaryKey is committee_id with employee_id
    protected $primaryKey = 'virtual_committee_member_id';
    
    public function Employee(){
        return $this->belongsTo(Employee::class,'employee_id');
    }
    public function VirtualCommitteeTender(){
        return $this->belongsTo(VirtualCommitteeTender::class,'virtual_committee_tender_id');
    }
    public function Judgment(){
        return $this->hasMany(Judgment::class,'virtual_committee_member_id');
    }
}
