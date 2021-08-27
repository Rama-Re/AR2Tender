<?php

namespace App\Http\Controllers\CommitteeControllers;

use App\Http\Controllers\AccountControllers\UserAuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Http\Controllers\MyValidator;
use App\Models\Account\Employee;
use App\Models\CommitteeRelations\Committee;
use App\Models\CommitteeRelations\CommitteeMember;
use App\Models\CommitteeRelations\VirtualCommittee;
use App\Models\CommitteeRelations\VirtualCommitteeMember;
use App\Models\TenderRelated\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommitteeController extends Controller
{
    public static function validation(Request $request){
        $data = $request->only('tender_id','type','members');
        $rules = [
            'tender_id' => 'required|exists:tenders,tender_id',
            'type' => 'required|in:financial,technician,decision maker',
            'members' => 'array|required',
            'members.*.task'=> 'required|in:administrator,member',
            'members.*.employee_id'=> 'required|exists:employees,employee_id',
        ];
        return MyValidator::validation($data,$rules);
    }
    public function create(Request $request){
        $result = $this->validation($request);
        if($result["status"]){
            $committee = new Committee;
            $committee->tender_id = $request->tender_id;
            $committee->type = $request->type;
            $checkMember = Committee::where('tender_id',$request->tender_id)->where('type',$request->type)->get()->first();
            if($checkMember) return response()->json(GeneralTrait::returnError('403','this type of committee is exist'));
            $committee->save();
            if(!$committee){
                return response()->json(GeneralTrait::returnError('403','wrong in add committee try again later'));
            }
            $addMembers = (new CommitteeMemberController)->addMembers($committee->committee_id,$request->members);
            if(!$addMembers){
                $committee->delete();
                return response()->json(GeneralTrait::returnError('403','wrong in add committee members'));
            }
            //Company created, return success response
            return response()->json(GeneralTrait::returnData('committee',$committee,'Committee created successfully'));
        }
        else return response()->json($result);
    }
    public function addVirtualCommittee(Request $request)
    {
        $result = MyValidator::validation($request->only('tender_id','virtual_committee_id'),
        [
            'tender_id' => 'required|exists:tenders,tender_id',
            'virtual_committee_id'=> 'required'
        ]);

        if($result["status"]){
            $virtual_committee_id = null;
            $virtualCommittee = VirtualCommitteeController::index($request);
            if($virtualCommittee){
                $virtual_committee_id = $virtualCommittee->virtual_committee_id;
                $virtualCommitteeMembers = VirtualCommitteeMemberController::index($virtual_committee_id);
                if($virtualCommitteeMembers){
                    $committee = new Committee;
                    $committee->tender_id = $request->tender_id;
                    $committee->type = $virtualCommittee->type;
                    $committee->save();
                    if(!$committee){
                        return response()->json(GeneralTrait::returnError('403','Some thing went wrong'));
                    }
                    $members = new CommitteeMemberController;
                    $result = $members->addVirtualCommitteeMembers($committee->committee_id,$virtualCommitteeMembers);
                    if($result) return response()->json(GeneralTrait::returnSuccessMessage("virtual committee added successfully"));
                    return response()->json(GeneralTrait::returnError('401','members of VirtualCommittee can\'t be added'));
                }
                return response()->json(GeneralTrait::returnError('404','this VirtualCommittee doesn\'t have any members'));
            }
            return response()->json(GeneralTrait::returnError('404','this VirtualCommittee doesn\'t exist'));
        }
        else return response()->json($result);
    }
    public function getCommitteesOfEmployee(Request $request)
    {
        $user = (UserAuthController::getUser($request))['user'];
        $user_id = $user->user_id;
        $employee_id = Employee::where('user_id',$user_id)->get('employee_id')->first()->employee_id;
        $committees = CommitteeMember::join('committees','committees.committee_id','=','committee_members.committee_id')
        ->join('tenders','tenders.tender_id','=','committees.tender_id')
        ->where('committee_members.employee_id',$employee_id)
        ->get(['tenders.title','committees.committee_id','committees.type','committee_members.committee_member_id','committee_members.task']);
        if($committees) return response()->json(GeneralTrait::returnData('committees',$committees));
        return response()->json(GeneralTrait::returnError('404','you are not member in any committee until now'));
    }
    
    public function getCommitteesOfTender(Request $request)
    {
        if(!$request->has('tender_id')){
            return response()->json(GeneralTrait::returnError('404','tender_id is required'));
        }
        $committees = Tender::join('committees','committees.tender_id','=','tenders.tender_id')
        ->join('committee_members','committee_members.committee_id','=','committees.committee_id')
        ->join('employees','employees.employee_id','=','committee_members.employee_id')
        //->join('judgment_of_committee','judgment_of_committee.committee_member_id','=','committee_members.committee_member_id')
        ->join('tender_track','tender_track.tender_id','=','tenders.tender_id')
        ->where('tenders.tender_id',$request->tender_id)
        //select what front is need
        ->get();
        if($committees) return response()->json(GeneralTrait::returnData('committees',$committees));
        return response()->json(GeneralTrait::returnError('404','you are not member in any committee until now'));
    }

}
