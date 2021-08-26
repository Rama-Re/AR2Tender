<?php

namespace App\Http\Controllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Models\Account\FCMToken;
use Illuminate\Http\Request;

class FCMTokenController extends Controller
{
    public function saveFCMToken(Request $request)
    {
        $result = (UserAuthController::getUser($request));
        if(!$request->has('fcm_token')){
            return response()->json(GeneralTrait::returnError('404','fcm_token is required'));
        }
        $user_id = $result['user']->user_id;
        $count = FCMToken::where('fcm_token',$request->fcm_token)->count();
        if($count == 0){
            $fcm = new FCMToken;
            $fcm->user_id = $user_id;
            $fcm->fcm_token = $request->fcm_token;
            $fcm->save();
        }
        return response()->json(GeneralTrait::returnSuccessMessage('Token save successfully'));

    }   
}
