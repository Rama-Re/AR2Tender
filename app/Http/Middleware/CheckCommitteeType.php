<?php

namespace App\Http\Middleware;

use App\Http\Controllers\CommitteeController\CommitteeController;
use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\CommitteeController\CommitteeMemberController;
use App\Http\Controllers\GeneralTrait;

class CheckCommitteeType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next,...$types)
    {
        $committee = ((new CommitteeController)->getCommittee($request))['committee'];
        if(!$committee){
            return response()->json(GeneralTrait::returnError('404','wrong request'));
        }
        $committee_type = $committee->type;
        foreach ($types as $type){
            if ($committee_type == $type){
                return $next($request);
            }
        }
        return response()->json(GeneralTrait::returnError('404','you don\'t have premision to do that'));
    }
}
