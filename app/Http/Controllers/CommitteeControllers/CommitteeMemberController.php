<?php

namespace App\Http\Controllers\CommitteeController;

use App\Http\Controllers\AccountControllers\UserAuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Http\Controllers\MyValidator;
use App\Models\Account\Employee;
use App\Models\CommitteeRelations\Committee;
use App\Models\CommitteeRelations\CommitteeMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommitteeMemberController extends Controller
{
    public static function validation(Request $request){
        $data = $request->only('committee_id','employee_id','task');
        $rules = [
            'committee_id' => 'required',
            'employee_id' => 'required',
            'task' => 'required|in:administrator,viewer,discussant'
        ];
        return MyValidator::validation($data,$rules);
    }
    
    public function create(Request $request){
        $generalTrait = new GeneralTrait;
        $result = $this->validation($request);
        if($result["status"]){
            $committeeMember = new CommitteeMember;
            $committeeMember->committee_id = $request->committee_id;
            $committeeMember->employee_id = $request->employee_id;
            $committeeMember->task = $request->task;
            $committeeMember->save();
            if(!$committeeMember){
                return response()->json($generalTrait->returnError('403','Some thing went wrong'));
            }
            //Company created, return success response
            return response()->json($generalTrait->returnData('committeeMember',$committeeMember,'CommitteeMember created successfully'));
        }
        else return response()->json($result);
    }
    public function addMembers($committee_id, $members)
    {
        $status = true;
        foreach($members as $member){
            $committeeMember = new CommitteeMember;
            $committeeMember->committee_id = $committee_id;
            $committeeMember->employee_id = $member['employee_id'];
            $committeeMember->task = $member['task'];
            $committeeMember->save();
            if(!$committeeMember) $status = false;
        }
        return $status;
    }
    public function addVirtualCommitteeMembers($committee_id, $members)
    {
        foreach($members as $member){
            $committeeMember = new CommitteeMember;
            $committeeMember->committee_id = $committee_id;
            $committeeMember->employee_id = $member->employee_id;
            $committeeMember->task = $member->task;
            $committeeMember->save();
        }
        return true;
    }
    public static function getTenderId($committee_member_id)
    {
        $committee_id = CommitteeMember::find($committee_member_id)->get('committee_id')->first();
        if(!$committee_id) return -1;
        $tender_id = Committee::find($committee_id->committee_id)->get('tender_id')->first();
        if(!$tender_id) return -1;
        return $tender_id->tender_id;
    }
    public function getCommitteeMemberFromToken(Request $request)
    {
        if(!$request->has('tender_id')){
            return GeneralTrait::returnError('404','tender_id is required');
        }
        $user = (UserAuthController::getUser($request))['user'];
        $user_id = $user->user_id;
        $employee_id = Employee::where('user_id',$user_id)->get('employee_id')->first()->employee_id;
        $committee_member = CommitteeMember::where('employee_id',$employee_id)
        ->where('tender_id',$request->tender_id)->get()->first();
        if($committee_member) return GeneralTrait::returnData('committee_member',$committee_member);
        return GeneralTrait::returnError('404','you are not member at any committee of this tender');
    }
}
