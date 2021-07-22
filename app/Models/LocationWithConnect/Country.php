<?php

namespace App\Models\LocationWithController;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'country_name',
        'num_code'
    ];

    protected $primaryKey = 'country_id';

    public function Location(){
        return $this->hasMany(Location::class,'country_id');
    }
}
