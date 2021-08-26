<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{   
    public static function getNoti($title, $body, $user_id){
        $noti = new Notification;
        $noti->title = $title;
        $noti->body = $body;
        $noti->user_id = $user_id;
        $noti->save();
        if($noti) {
            return [
                'title' => $title,
                'body' => $body,
                'status'=>true
            ];
        }
        else return ['status'=>false];
    }
}
