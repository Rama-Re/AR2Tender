<?php

namespace App\Http\Middleware;

use App\Http\Controllers\GeneralTrait;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException as TokenInvalid;
use Tymon\JWTAuth\Exceptions\TokenExpiredException as TokenExpired;
class CheckUserToken
{
    public function handle(Request $request, Closure $next)
    {
        $generalTrait = new GeneralTrait;
        $user = null;
        try{
            $user = JWTAuth::parseToken()->authenticate();
        } catch(\Exception $e){
            if($e instanceof TokenInvalid)
                return response()->json($generalTrait -> returnError('401','INVALID_TOKEN'));
                
                else if($e instanceof TokenExpired)
                return response()->json($generalTrait -> returnError('401','EXPIRED_TOKEN'));
                
                else
                return response()->json($generalTrait -> returnError('401','TOKEN_NOTFOUND'));
            } catch(\Throwable $e){
                if($e instanceof TokenInvalid)
                return response()->json($generalTrait -> returnError('401','INVALID_TOKEN'));
                
                else if($e instanceof TokenExpired)
                return response()->json($generalTrait -> returnError('401','EXPIRED_TOKEN'));
                
                else
                return $generalTrait -> returnError('401','TOKEN_NOTFOUND');
        }
        if(!$user){
            return response()->json($generalTrait->returnError('401','Authorization Token not found'));
        }
        return $next($request);
    }
}
