<?php

namespace App\Models\LocationWithController;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_id',
        'number',
        'company_location_id',
    ];
    
    protected $primaryKey = 'phone_id';
    
    public function CompanyLocation(){
        return $this->belongsTo(CompanyLocation::class,'company_id');
    }
}
