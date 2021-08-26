<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = [
        'notification_id',
        'title',
        'body',
        'user_id'
    ];
    protected $primaryKey = 'notification_id';
    public function User(){
        return $this->hasOne(User::class);
    }
}
