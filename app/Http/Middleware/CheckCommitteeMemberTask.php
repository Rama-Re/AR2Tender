<?php

namespace App\Http\Middleware;

use App\Http\Controllers\CommitteeControllers\CommitteeMemberController;
use App\Http\Controllers\GeneralTrait;
use Closure;
use Illuminate\Http\Request;

class CheckCommitteeMembertask
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next,...$tasks)
    {
        $committee_member = ((new CommitteeMemberController)->getCommitteeMemberFromToken($request))['committee_member'];
        if(!$committee_member){
            return response()->json(GeneralTrait::returnError('404','wrong request'));
        }
        $member_task = $committee_member->task;
        foreach ($tasks as $task){
            if ($member_task == $task){
                return $next($request);
            }
        }
        return response()->json(GeneralTrait::returnError('404','you don\'t have premision to do that'));
    }
}
