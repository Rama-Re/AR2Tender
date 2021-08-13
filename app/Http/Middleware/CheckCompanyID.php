<?php

namespace App\Http\Middleware;

use App\Http\Controllers\GeneralTrait;
use App\Models\Account\Company;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckCompanyID
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
        $generalTrait = new GeneralTrait;
        $user = JWTAuth::parseToken()->authenticate();
        $user_id = $user->user_id;
        $company_id = Company::join('users','users.user_id','companies.user_id')->where('user_id',$user_id)->first()->get('company_id');
        if($company_id == $request->company_id){
            return $next($request);
        }
        return response()->json($generalTrait->returnError('401','this isn\'t your id'));
    }
}