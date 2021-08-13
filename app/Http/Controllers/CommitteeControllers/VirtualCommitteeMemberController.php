<?php

namespace App\Http\Controllers\CommitteeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Http\Controllers\MyValidator;
use App\Models\CommitteeRelations\VirtualCommitteeMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VirtualCommitteeMemberController extends Controller
{
    public static function validation(Request $request){
        $data = $request->only('virtual_committee_id','employee_id','task');
        $rules = [
            'virtual_committee_id' => 'required',
            'employee_id' => 'required',
            'task' => 'required|in:administrator,viewer,discussant'
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
            $virtualCommitteeMember->save();
            if(!$virtualCommitteeMember){
                return response()->json($generalTrait->returnError('403','Some thing went wrong'));
            }
            //Company created, return success response
            return response()->json($generalTrait->returnData('virtualCommitteeMember',$virtualCommitteeMember,'VirtualCommitteeMember created successfully'));
        }
        else return response()->json($result);
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
        $virtualCommitteeMembers = VirtualCommitteeMember::where('virtual_committee_id',$request->virtual_committee_id)->get()->first();
        return $virtualCommitteeMembers;
    }
}
