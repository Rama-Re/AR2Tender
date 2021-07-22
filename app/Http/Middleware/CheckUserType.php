<?php

namespace App\Http\Middleware;

use App\Http\Controllers\GeneralTrait;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;


class CheckUserType
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
        $generalTrait = new GeneralTrait;
        $user = JWTAuth::parseToken()->authenticate();
        $type = $user->type;
        foreach ($roles as $value){
            if ($type == $value){
                return $next($request);
            }
        }
        return response()->json($generalTrait->returnError('401','you don\'t have premision to do that'));
    }
}
