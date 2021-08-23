<?php

namespace App\Http\Controllers\CommitteeController;

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
        $data = $request->only('employee_id','type');
        $rules = [
            'company_id' => 'required',
            'type' => 'required|in:financial,technician,decision_maker'
        ];
        return MyValidator::validation($data,$rules);
    }
    
    public function create(Request $request){
        $result = $this->validation($request);
        if($result["status"]){
            $virtualCommittee = new VirtualCommittee;
            $virtualCommittee->company_id = $request->company_id;
            $virtualCommittee->employee_id = $request->employee_id;
            $virtualCommittee->type = $request->type;
            $virtualCommittee->save();
            if(!$virtualCommittee){
                return response()->json(GeneralTrait::returnError('403','Some thing went wrong'));
            }
            return response()->json(GeneralTrait::returnData('virtualCommittee',$virtualCommittee,'VirtualCommittee created successfully'));
        }
        else return response()->json($result);
    }
    public function getVirtualCommittees(Request $request)
    {
        $virtualCommittees = VirtualCommittee::where('company_id',$request->company_id)->get();
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
