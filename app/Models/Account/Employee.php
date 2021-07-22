<?php

namespace App\Models\Account;
use App\Models\User;
use App\Models\Account\Company;

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
}
