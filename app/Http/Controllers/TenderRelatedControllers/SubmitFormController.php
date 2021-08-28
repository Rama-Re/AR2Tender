<?php
namespace App\Http\Controllers\TenderRelatedControllers;

use App\Http\Controllers\AccountControllers\CompanyController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Http\Controllers\MyValidator;
use App\Models\Account\Company;
use App\Models\TenderRelated\Submit_form;
use App\Models\TenderRelated\Tender;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class SubmitFormController extends Controller
{
    public static function getTenderId(Request $request)
    {
        $tender_id = Submit_form::find($request->submit_form_id)->get('tender_id')->first();
        if (!$tender_id) {
            return -1;
        }

        return $tender_id->tender_id;
    }
    public function store(Request $request)
    {
        // need to check if they can submit
        $generalTrait = new GeneralTrait;
        $result = CompanyController::checkAndGetCompanyID($request);
        $res = MyValidator::validation($request->only('tender_id'),[
            'tender_id' => 'required|exists:tenders,tender_id'
        ]);
        if(!$res['status']){
            return $res;
        }

        if (!is_numeric($result)) {
            // if the id is not numeric then it is a json response and not companyId
            return $result;
        }
        if ($result == Tender::where('tender_id', $request->tender_id)->value('company_id')) {
            return $generalTrait->returnError('400', "You can't submit to this tender");
        }
        if (Tender::where('tender_id', $request->tender_id)->value('active') == false) {
            return $generalTrait->returnError('400', "the tender is a draft you can't submit to it");
        }

        $started = TenderTrackController::checkAfterStart(new Carbon(now('UTC')), $request->tender_id);
        if (!$started) {
            return $generalTrait->returnError('400', "you cant submit the tender didn't start yet");
        }
        $available = TenderTrackController::checkBeforEnd(new Carbon(now('UTC')), $request->tender_id);
        if (!$available) {
            return $generalTrait->returnError('400', "the tender closed you can't submit");
        }
        $canSubmit = SelectiveTenderController::checkAbility($result, $request->tender_id);
        if (!$canSubmit) {
            return $generalTrait->returnError('400', "this tender is a selective tender and has conditions you don't meet");
        }
        $submission = new Submit_form;
        try {
            $submission->company_id = $result;
            $submission->tender_id = $request->tender_id;
        } catch (Exception $e) {
            return $generalTrait->returnError('401', $e->__toString());
            //return $generalTrait->returnError('401',"couldn't save the submission");
        }
        $submission->save();

        return $generalTrait->returnData('submit_id', $submission->submit_form_id);
    }
    public static function getCompaniesID($tender_id)
    {
        //$generalTrait = new GeneralTrait;
        $companiesID = Submit_form::where('tender_id', $tender_id)->pluck('company_id');
        //return $generalTrait->returnData('companiesId', $companiesID);
        return $companiesID;
    }
    public static function getCompaniesfCMToken($tender_id)
    {
        //$generalTrait = new GeneralTrait;
        $companiesEmails = Submit_form::join('companies','companies.company_id','=','submit_forms.company_id')
        ->join('users','users.user_id','=','companies.user_id')
        ->join('fcm_tokens','fcm_tokens.user_id','=','users.user_id')
        ->where('tender_id', $tender_id)->pluck('fcm_tokens.fcm_token');
        //return $generalTrait->returnData('companiesEmails', $companiesEmails);
        return $companiesEmails;

    }
}
