<?php

namespace App\Models\CommitteeRelations;

use App\Models\Account\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitteeMember extends Model
{
    use HasFactory;
    protected $fillable = [
        'committee_member_id',
        'committee_id',
        'employee_id',
        //what the meaning of task + add_date
        'task',
        'add_date'
    ];
    //primaryKey is committee_id with employee_id
    protected $primaryKey = 'committee_member_id';
    
    public function Employee(){
        return $this->belongsTo(Employee::class,'employee_id');
    }
    public function Committee(){
        return $this->belongsTo(Committee::class,'committee_id');
    }
    public function JudgmentOfCommittee(){
        return $this->hasMany(JudgmentOfCommittee::class,'committee_member_id');
    }
    public function TenderResult(){
        return $this->hasOne(TenderResult::class,'committee_member_id');
    }
}
