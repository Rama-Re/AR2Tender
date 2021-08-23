<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\GeneralTrait;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException as TokenInvalid;
use Tymon\JWTAuth\Exceptions\TokenExpiredException as TokenExpired;
class VerificationUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->hasHeader('token')){
            $token = $request->header('token');
            $request->headers->set('token',(string)$token,true);
            $request->headers->set('Authorization','Bearer '.$token,true);
            $user = JWTAuth::parseToken()->authenticate($request);
            if ($user->is_verified) {
                return $next($request);
            }
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(GeneralTrait::returnError('403','Your account isn\'t verified'));
        }
        else if($request->has('email')){
            $user = User::where('email',$request->email)->get()->first();
            if($user->is_verified){
                return $next($request);
            }
            return response()->json(GeneralTrait::returnError('403','Your account isn\'t verified'));
        }
        return response()->json(GeneralTrait::returnError('404','email is required'));
    }
}
