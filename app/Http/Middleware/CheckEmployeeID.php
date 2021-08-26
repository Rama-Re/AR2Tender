<?php

namespace App\Http\Middleware;

use App\Http\Controllers\GeneralTrait;
use App\Models\Account\Employee;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckEmployeeID
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
        $token = $request->header('token');
        $request->headers->set('token',(string)$token,true);
        $request->headers->set('Authorization','Bearer '.$token,true);
        $user = JWTAuth::parseToken()->authenticate($request);
        $user_id = $user->user_id;
        $company_id = Employee::join('users','users.user_id','employees.user_id')->where('user_id',$user_id)->first()->get('company_id');
        if($company_id == $request->company_id){
            return $next($request);
        }
        return response()->json($generalTrait->returnError('401','this isn\'t your id'));
    }
}
