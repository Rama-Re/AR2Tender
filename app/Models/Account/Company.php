<?php

namespace App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'location_id',
        'company_name',
        'Director_name',
        'username',
        'about_us'
    ];

    public function User(){
        return $this->belongsTo(User::class);
    }
    
}
