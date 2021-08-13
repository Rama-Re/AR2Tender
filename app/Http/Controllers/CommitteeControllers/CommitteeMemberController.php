<?php

namespace App\Http\Controllers\CommitteeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Http\Controllers\MyValidator;
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
    public function addVirtualCommitteeMembers($committee_id, $members)
    {
        foreach($members as $member){
            $committeeMember = new CommitteeMember;
            $committeeMember->committee_id = $committee_id;
            $committeeMember->employee_id = $member->employee_id;
            $committeeMember->task = $member->task;
            $committeeMember->save();
            if(!$committeeMember) return false;
        }
        return true;
    }
}
