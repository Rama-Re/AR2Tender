<?php

namespace App\Http\Controllers\JudgmentControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Http\Controllers\MyValidator;
use GrahamCampbell\ResultType\Result;
use Illuminate\Http\Request;
use App\Models\Judgment\TenderResult;
use App\Http\Controllers\CommitteeController\CommitteeMemberController;
use App\Http\Controllers\TenderRelatedControllers\SubmitFormController;
use App\Models\TenderRelated\Tender;

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
            $temp = TenderResult::where('committee_member_id',$request->committee_member_id)
            ->where('tender_id',$tender_id1)->get()->first();
            if($temp) return response()->json(GeneralTrait::returnError('401','Desicion added before'));
            $judgment->tender_id = $tender_id1;
            $judgment->committee_member_id = $committee_member_id;
            $judgment->submit_form_id = $request->submit_form_id;
            $judgment->save();
            if(!$judgment) return response()->json(GeneralTrait::returnError('401','something went wrong'));
            return response()->json(GeneralTrait::returnSuccessMessage('Desicion added succesfully'));
        }
        return response()->json($result);
    }
}
