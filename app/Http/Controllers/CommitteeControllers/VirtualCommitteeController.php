<?php

namespace App\Http\Controllers\CommitteeControllers;

use App\Http\Controllers\AccountControllers\CompanyController;
use App\Http\Controllers\AccountControllers\UserAuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Http\Controllers\MyValidator;
use App\Models\Account\Company;
use App\Models\CommitteeRelations\VirtualCommittee;
use App\Models\CommitteeRelations\VirtualCommitteeMember;
use GrahamCampbell\ResultType\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VirtualCommitteeController extends Controller
{
    public static function validation(Request $request){
        $data = $request->only('company_id','type','members');
        $rules = [
            'company_id' => 'required',
            'type' => 'required|in:financial,technician,decision maker',
            'members' => 'required|array',
            'members.*.task'=> 'required',
            'members.*.employee_id'=> 'required',
        ];
        return MyValidator::validation($data,$rules);
    }
    
    public function create(Request $request){
        $result = $this->validation($request);
        if($result["status"]){
            $virtualCommittee = new VirtualCommittee;
            $company_id = CompanyController::checkAndGetCompanyID($request);
            $virtualCommittee->company_id = $company_id;
            $virtualCommittee->type = $request->type;
            $checkMember = VirtualCommittee::where('company_id',$company_id)->where('type',$request->type)->get()->first();
            if($checkMember) return response()->json(GeneralTrait::returnError('403','this type of committee is exist'));
            $virtualCommittee->save();
            if(!$virtualCommittee){
                return response()->json(GeneralTrait::returnError('403','Some thing went wrong'));
            }
            $addMembers = (new VirtualCommitteeMemberController)->addMembers($virtualCommittee->virtual_committee_id,$request->members);
            if(!$addMembers){
                $virtualCommittee->delete();
                return response()->json(GeneralTrait::returnError('403','wrong in add committee members'));
            }
            return response()->json(GeneralTrait::returnData('virtualCommittee',$virtualCommittee,'VirtualCommittee created successfully'));
        }
        else return response()->json($result);
    }

    public function getVirtualCommittees(Request $request)
    {
        $virtualCommittees = VirtualCommittee::join('virtual_committee_members','virtual_committee_members.virtual_committee_id','=','virtual_committees.virtual_committee_id')
        ->join('employees','employees.employee_id','=','virtual_committee_members.employee_id')
        ->where('virtual_committees.company_id',$request->company_id)->get();
        if($virtualCommittees){
            return GeneralTrait::returnData('virtualCommittees',$virtualCommittees,"suscess");
        }
        return GeneralTrait::returnError('404',"this company doesn\'t have virtual committees");
    }
    public static function index(Request $request)
    {
        return VirtualCommittee::where('virtual_committee_id',$request->virtual_committee_id)->get()->first();
    }
}
