<?php

namespace App\Models\LocationWithConnect;

use App\Models\TenderRelated\SelectiveCountry;
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
    public $incrementing = false;
    protected $keyType = 'char';

    protected $primaryKey = 'country_id';
    public function Location(){
        return $this->hasMany(Location::class,'country_id');
    }
    public function SelectiveCountry(){
        return $this->hasMany(SelectiveCountry::class,'country_id');
    }

    
}
