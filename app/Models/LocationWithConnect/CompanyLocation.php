<?php

namespace App\Models\LocationWithController;

use App\Models\Account\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyLocation extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_location_id',
        'company_id',
        'location_id',
    ];
    protected $casts = [
        'set_up_date' => 'datetime',
    ];
    public function Location(){
        return $this->belongsTo(Location::class,'location_id');
    }
    
    public function Company(){
        return $this->belongsTo(Company::class,'company_id');
    }

}
