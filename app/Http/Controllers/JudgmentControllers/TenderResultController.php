<?php

namespace App\Http\Controllers\JudgmentControllers;

use App\Http\Controllers\AccountControllers\CompanyController;
use App\Http\Controllers\AccountControllers\UserAuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Http\Controllers\MyValidator;
use GrahamCampbell\ResultType\Result;
use Illuminate\Http\Request;
use App\Models\Judgment\TenderResult;
use App\Http\Controllers\CommitteeControllers\CommitteeMemberController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TenderRelatedControllers\SubmitFormController;
use App\Http\Controllers\TenderRelatedControllers\TenderTrackController;
use App\Models\Account\Company;
use App\Models\TenderRelated\Tender;
use Carbon\Carbon;

class TenderResultController extends Controller
{
    //'tender_result_id','submit_form_id','committee_member_id', 'tender_id',
    public static function validation(Request $request){
        $data = $request->only('submit_form_id','committee_member_id','tender_id');
        $rules = [
            'submit_form_id' => 'required',
            'committee_member_id' => 'required',
            'tender_id' => 'required',
        ];
        return MyValidator::validation($data,$rules);
    }
    public function getTenderResult(Request $request)
    {
        $judgment = null;
        $judgment = TenderResult::where('tender_id',$request->tender_id)->get('submit_form_id')->first()->submit_form_id;
        return response()->json(GeneralTrait::returnData('TenderResult',$judgment));
    }
    public function addTenderResult(Request $request)
    {
        $result = $this->validation($request);
        if($result['status']){
            $judgment = new TenderResult;
            $committee_member_id = ((new CommitteeMemberController)->getCommitteeMemberFromToken($request))['committee_member']->committee_member_id;
            $tender_id1 = (new CommitteeMemberController)->getTenderId($$committee_member_id);
            if($tender_id1 == -1) return response()->json(GeneralTrait::returnError('404','tender is not found'));
            $tender_id2 = (new SubmitFormController)->getTenderId($request);
            if($tender_id2 == -1) return response()->json(GeneralTrait::returnError('404','tender is not found'));
            if($tender_id1 != $tender_id2) return response()->json(GeneralTrait::returnError('403','wrong request'));
            if((new TenderTrackController)->checkDecisionCommitteeJudgmentDate($tender_id1)){
            $temp = TenderResult::where('committee_member_id',$request->committee_member_id)
            ->where('tender_id',$tender_id1)->get()->first();
            if($temp) return response()->json(GeneralTrait::returnError('401','Desicion added before'));
            $judgment->tender_id = $tender_id1;
            $judgment->committee_member_id = $committee_member_id;
            $judgment->submit_form_id = $request->submit_form_id;
            $judgment->save();
            if(!$judgment) return response()->json(GeneralTrait::returnError('401','something went wrong'));
            return response()->json(GeneralTrait::returnData('tender_result',$judgment,'Desicion added successfully'));
        }
        return response()->json(GeneralTrait::returnError('401','this is not your judgment time'));
        }
        return response()->json($result);
    }
    public function getResultOfMyOffers(Request $request)
    {
        $company_id = CompanyController::checkAndGetCompanyID($request);
        $with_result = Tender::join('tender_result','tender_result.tender_id','=','tenders.tender_id')
        ->join('submit_forms','submit_forms.submit_form_id','=','tender_result.submit_form_id')
        ->where('submit_forms.company_id',$company_id)
        ->orderBy('submit_forms.created_at','asc');
        $without_result = Tender::rightJoin('tender_result','tender_result.tender_id','!=','tenders.tender_id')
        ->join('tender_track','tender_track.tender_id','=','tenders.tender_id')
        ->where('tender_track.decision_committee_judgment_date_end','>',(new Carbon(now('UTC'))))
        ->where('submit_forms.company_id',$company_id)
        ->orderBy('submit_forms.created_at','asc');
        
        return response()->json(GeneralTrait::returnData('offers',compact('with_result','without_result')));
    }
    
    public function notifysubmittedUsers(Request $request)
    {
        $user_id = UserAuthController::getUser($request)['user']->user_id;
        $result = MyValidator::validation($request->only('tender_result_id'),['tender_result_id'=>'required']);
        if($result['status']){
            $tender_name = Tender::where('tender_id',$request->tender_id)->get('tender_name')->first()->tender_name;
            $receivers = 
            

            if(!$receivers) return response()->json(GeneralTrait::returnError('403','failed request'));
            $data = NotificationController::getNoti($tender_name,'You lost',$user_id);
            if(!$data['status']){
                return response()->json(GeneralTrait::returnError('404','couldn\'t generate notifications'));
            }
            $notify1 = compact('receivers','data');
            $receivers = TenderResult::join('tenders','tenders.tender_id','=','')
            
            
            if(!$receivers) return response()->json(GeneralTrait::returnError('403','failed request'));
            $data = NotificationController::getNoti($tender_name,'You won',$user_id);
            if(!$data['status']){
                return response()->json(GeneralTrait::returnError('404','couldn\'t generate notifications'));
            }
            $notify2 = compact('receivers','data');
            return response()->json(GeneralTrait::returnData('notify',compact('notify1','notify2')));
        }
        return response()->json($result);
    }
    
}

