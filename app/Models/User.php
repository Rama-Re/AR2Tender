<?php

namespace App\Models;
use App\Models\Account\Employee;
use App\Models\Account\Company;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject ,MustVerifyEmail
{
    use HasFactory, Notifiable;


    protected $fillable = [
        'email',
        'user_id',
        'type',
        'is_verified',
        'blocked'
    ];
       
    protected $primaryKey = 'user_id';

    protected $hidden = [
        'password',
        'confirm_code',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    public function Company(){
        return $this->hasOne(Company::class);
    }
    public function Employee(){
        return $this->hasOne(Employee::class);
    }
    public function Admin(){
        return $this->hasOne(Employee::class);
    }
}
