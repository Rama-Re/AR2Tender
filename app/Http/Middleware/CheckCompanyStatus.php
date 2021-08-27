<?php

namespace App\Http\Middleware;

use App\Http\Controllers\GeneralTrait;
use App\Models\Account\Company;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckCompanyStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next,...$roles)
    {
        $token = $request->header('token');
        $request->headers->set('token',(string)$token,true);
        $request->headers->set('Authorization','Bearer '.$token,true);
        $user = JWTAuth::parseToken()->authenticate($request);
        $status = Company::where('user_id',$user->user_id)->get('status')->first()->status;
        foreach ($roles as $value){
            if ($status == $value){
                return $next($request);
            }
        }
        return response()->json(GeneralTrait::returnError('401','you don\'t have premision to do that'));
    }
}
