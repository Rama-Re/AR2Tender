<?php

namespace App\Models\Account;
use App\Models\User;
use App\Models\Account\Company;
use App\Models\CommitteeRelations\VirtualCommitteeMember;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'employee_name',
        'user_id',
        'company_id'
    ];

    protected $primaryKey = 'employee_id';

    public function User(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function Company(){
        return $this->belongsTo(Company::class,'company_id');
    }
    public function VirtualCommitteeMember(){
        return $this->hasMany(VirtualCommitteeMember::class,'employee_id');
    }
    public function CommitteeMember(){
        return $this->hasMany(CommitteeMember::class,'employee_id');
    }
}
