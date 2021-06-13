<?php

namespace App\Models;
use App\Models\Account\Employee;
use App\Models\Account\Company;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'company_id',
        'employee_id',
        'email',
        'type',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function Company(){
        return $this->hasOne(Company::class);
    }
    public function Employee(){
        return $this->hasOne(Employee::class);
    }
}
