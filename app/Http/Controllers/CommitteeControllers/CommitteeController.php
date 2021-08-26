<?php

namespace App\Http\Controllers\CommitteeController;

use App\Http\Controllers\AccountControllers\UserAuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Http\Controllers\MyValidator;
use App\Models\CommitteeRelations\Committee;
use App\Models\CommitteeRelations\VirtualCommittee;
use App\Models\CommitteeRelations\VirtualCommitteeMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommitteeController extends Controller
{
    public static function validation(Request $request){
        $data = $request->only('tender_id','type');
        $rules = [
            'tender_id' => 'required',
            'type' => 'required|in:financial,technician,decision maker'
        ];
        return MyValidator::validation($data,$rules);
    }
    public function create(Request $request){
        $result = $this->validation($request);
        if($result["status"]){
            $committee = new Committee;
            $committee->tender_id = $request->tender_id;
            $committee->type = $request->type;
            $committee->save();
            if(!$committee){
                return response()->json(GeneralTrait::returnError('403','Some thing went wrong'));
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
            'tender_id' => 'required',
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
    public function getCommitteeFromToken(Request $request)
    {
        if(!$request->has('tender_id')){
            return GeneralTrait::returnError('404','tender_id is required');
        }
        $committee_member = (new CommitteeMemberController)->getCommitteeMemberFromToken($request);
        if(!$committee_member['status']){
            return response()->json(GeneralTrait::returnError('404','you are not member at any committee of this tender'));
        }
        $committee_id = $committee_member['committee_member']->committee_id;
        $committee = Committee::find($committee_id)->get();
        if($committee) return response()->json(GeneralTrait::returnData('committee',$committee));
        return response()->json(GeneralTrait::returnError('404','you are not member at any committee of this tender'));
    }
}
