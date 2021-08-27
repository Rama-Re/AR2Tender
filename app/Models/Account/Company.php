<?php

namespace App\Models\Account;
use App\Models\User;
use App\Models\Account\Employee;
use App\Models\CommitteeRelations\VirtualCommittee;
use App\Models\LocationWithController\CompanyLocation;
use App\Models\LocationWithController\Phone;
use App\Models\TenderRelated\SelectiveCompany;
use App\Models\TenderRelated\Submit_form;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;


class Company  extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'company_name',
        'director_name',
        'username',
        'image',
        'image_path',
        'specialty',
        'status',
        'about_us'
    ];

    protected $primaryKey = 'company_id';

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

    public function User(){
        return $this->belongsTo(User::class,'user_id');
    }
    
    public function Employee(){
        return $this->hasMany(Employee::class,'company_id');
    }
    
    public function CompanyLocation(){
        return $this->hasMany(CompanyLocation::class,'company_id');
    }
    
    public function VirtualCommittee(){
        return $this->hasMany(VirtualCommittee::class,'company_id');
    }
    
    public function SelectiveCompany(){
        return $this->hasMany(SelectiveCompany::class,'company_id');
    }
    public function Submit_form(){
        return $this->hasMany(Submit_form::class,'company_id');
    }

    
}
