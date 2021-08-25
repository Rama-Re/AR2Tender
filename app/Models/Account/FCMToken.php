<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FCMToken extends Model
{
    use HasFactory;
    protected $fillable = [
        'fcm_token_id',
        'user_id',
        'fcm_token',
    ];
    protected $primaryKey = 'fcm_token_id';

    public function User(){
        return $this->belongsTo(User::class,'user_id');
    }
}
