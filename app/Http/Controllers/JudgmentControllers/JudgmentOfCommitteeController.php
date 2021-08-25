<?php

namespace App\Http\Controllers\JudgmentControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Http\Controllers\MyValidator;
use App\Models\Judgment\JudgmentOfCommittee;
use Illuminate\Http\Request;
use App\Http\Controllers\CommitteeController\CommitteeMemberController;
use App\Http\Controllers\TenderRelatedControllers\SubmitFormController;
use App\Models\TenderRelated\Submit_form;

class JudgmentOfCommitteeController extends Controller
{
    // 'committee_judgment_id','submit_form_id','committee_member_id','judgment','vote'
    public static function validation(Request $request){
        $data = $request->only('submit_form_id','committee_member_id','judgment','vote');
        $rules = [
            'submit_form_id' => 'required',
            'committee_member_id' => 'required',
            'judgment' => 'required',
            'vote' => 'required|min:0|max:100'
        ];
        return MyValidator::validation($data,$rules);
    }
    public function getJudgmentOfCommittee(Request $request)
    {
        $judgment = null;
        if($request->has('committee_judgment_id')) {
            $judgment = JudgmentOfCommittee::find($request->committee_judgment_id)->get()->first();
        }
        else if($request->has('submit_form_id') && $request->has('committee_member_id')){
            $judgment = JudgmentOfCommittee::where('committee_judgment_id',$request->committee_judgment_id)
            ->where('submit_form_id',$request->submit_form_id)->get()->first();
        }
        else if($request->has('submit_form_id')) {
            $judgment = JudgmentOfCommittee::where('submit_form_id',$request->submit_form_id)->get();
        }
        else if($request->has('committee_member_id')){
            $judgment = JudgmentOfCommittee::where('committee_judgment_id',$request->committee_judgment_id)->get();
        }
        else {
            return response()->json(GeneralTrait::returnError('400','committee_judgment_id is required'));
        }
        return response()->json(GeneralTrait::returnData('JudgmentOfCommittee',$judgment));
    }
    public function addJudgmentOfCommittee(Request $request)
    {
        $result = $this->validation($request);
        if($result['status']){
            $judgment = new JudgmentOfCommittee;
            $judgment->submit_form_id = $request->submit_form_id;
            $committee_member_id = ((new CommitteeMemberController)->getCommitteeMemberFromToken($request))['committee_member']->committee_member_id;
            $tender_id1 = (new CommitteeMemberController)->getTenderId($committee_member_id);
            if($tender_id1 == -1) return response()->json(GeneralTrait::returnError('404','tender is not found'));
            $tender_id2 = (new SubmitFormController)->getTenderId($request);
            if($tender_id2 == -1) return response()->json(GeneralTrait::returnError('404','tender is not found'));
            if($tender_id1 != $tender_id2) return response()->json(GeneralTrait::returnError('403','wrong request'));
            $temp = JudgmentOfCommittee::where('committee_member_id',$request->committee_member_id)
            ->where('submit_form_id',$request->submit_form_id)->get()->first();
            if($temp) return response()->json(GeneralTrait::returnError('401','Judgment added before'));
            $judgment->committee_member_id = $committee_member_id;
            $judgment->judgment = $request->judgment;
            $judgment->vote = $request->vote;
            $judgment->save();
            if(!$judgment) return response()->json(GeneralTrait::returnError('401','something went wrong'));
            return response()->json(GeneralTrait::returnSuccessMessage('judgment added successfully'));
        }
        return response()->json($result);
    }
}
