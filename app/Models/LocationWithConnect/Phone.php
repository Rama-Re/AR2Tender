<?php

namespace App\Models\LocationWithConnect;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_id',
        'phone_number',
        'company_location_id',
    ];
    
    protected $primaryKey = 'phone_id';
    
    public function CompanyLocation(){
        return $this->belongsTo(CompanyLocation::class,'company_id');
    }
}
