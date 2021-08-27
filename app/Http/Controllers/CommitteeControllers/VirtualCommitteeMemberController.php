<?php

namespace App\Http\Controllers\CommitteeControllers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Http\Controllers\MyValidator;
use App\Models\CommitteeRelations\VirtualCommitteeMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CommitteeRelations\VirtualCommittee;

class VirtualCommitteeMemberController extends Controller
{
    public static function validation(Request $request){
        $data = $request->only('virtual_committee_id','employee_id','task');
        $rules = [
            'virtual_committee_id' => 'required',
            'employee_id' => 'required',
            'task' => 'required|in:administrator,member'
        ];
        return MyValidator::validation($data,$rules);
    }
    
    public function create(Request $request){
        $generalTrait = new GeneralTrait;
        $result = $this->validation($request);
        if($result["status"]){
            $virtualCommitteeMember = new VirtualCommitteeMember;
            $virtualCommitteeMember->virtual_committee_id = $request->virtual_committee_id;
            $virtualCommitteeMember->employee_id = $request->employee_id;
            $virtualCommitteeMember->task = $request->task;
            $checkMember = VirtualCommitteeMember::where('virtual_committee_id',$request->virtual_committee_id)
            ->where('employee_id',$request->employee_id)
            ->where('task',$request->task)->get()->first();
            if($checkMember) 
            return response()->json($generalTrait->returnError('403','this member is already added'));
            $virtualCommitteeMember->save();
            if(!$virtualCommitteeMember){
                return response()->json($generalTrait->returnError('403','Some thing went wrong'));
            }
            //Company created, return success response
            return response()->json($generalTrait->returnData('virtualCommitteeMember',$virtualCommitteeMember,'VirtualCommitteeMember created successfully'));
        }
        else return response()->json($result);
    }
    public function addMembers($committee_id, $members)
    {
        $status = true;
        foreach($members as $member){
            $committeeMember = new VirtualCommitteeMember;
            $committeeMember->virtual_committee_id = $committee_id;
            $committeeMember->employee_id = $member['employee_id'];
            $committeeMember->task = $member['task'];
            $checkMember = VirtualCommitteeMember::where('virtual_committee_id',$committee_id)->where('employee_id',$member['employee_id'])->get()->first();
            if($checkMember) continue;
            $committeeMember->save();
            if(!$committeeMember) $status = false;
        }
        return $status;
    }
    public function getVirtualCommitteeMembers(Request $request)
    {
        $virtualCommitteeMembers = VirtualCommitteeMember::where('virtual_committee_id',$request->virtual_committee_id)->get();
        if($virtualCommitteeMembers){
            return GeneralTrait::returnData('virtualCommitteeMembers',$virtualCommitteeMembers,"suscess");
        }
        return GeneralTrait::returnError('404',"this committee doesn\'t have Members");
    }
    public static function index(Request $request)
    {
        $virtualCommitteeMembers = VirtualCommitteeMember::where('virtual_committee_id',$request->virtual_committee_id)->get();
        return $virtualCommitteeMembers;
    }
}
