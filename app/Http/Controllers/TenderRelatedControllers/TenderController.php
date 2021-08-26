<?php

namespace App\Http\Controllers\TenderRelatedControllers;

use App\Http\Controllers\AccountControllers\CompanyController;
use App\Http\Controllers\AccountControllers\UserAuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Http\Controllers\TenderRelatedControllers\TenderTrackController;
use App\Http\Controllers\MyValidator;
use App\Http\Controllers\NotificationController;
use App\Models\Account\Company;
use App\Models\TenderRelated\SelectiveCompany;
use App\Models\TenderRelated\Tender;
use App\Models\TenderRelated\Tender_file;
use App\Models\TenderRelated\Tender_track;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class TenderController extends Controller
{
    //use GeneralTrait;
    //get all tenders
    public function indexAdmin(Request $request)
    {
        //check the token from the request
        $generalTrait = new GeneralTrait;
        $order = ($request->order == "asc" || $request->order == "desc") ? $request->order : "desc";
        $tendersfromDB = Tender::index()->orderBy('tender_track.created_at', $order)->get();
        return $generalTrait->returnData('tenders', $tendersfromDB);
    }

    public function index($order, $dateFilterTenderTrack)
    {
        //check the token from the request
        $tendersfromDB = Tender::index()
            ->public()
            ->orderby($dateFilterTenderTrack ? $dateFilterTenderTrack : 'tender_track.created_at', $order)
            ->get(); // order by latest

        return $tendersfromDB;

    }

    public function indexSearch(Request $request)
    {
        //checked
        $generalTrait = new GeneralTrait();
        
        $search = $request->search;

        $tendersfromDB = Tender::index()
            ->public()
            ->where('Title', 'like', '%' . $search . '%')
            ->orWhere('company_name', 'like', '%' . $search . '%')
            ->get();

        $sortedTenders = $tendersfromDB->sortBy(function ($tender, $key) use ($search) {
            $rest = strlen($tender->Title) - strlen($search);
            return $rest;
        });

        return $generalTrait->returnData('tenders', $sortedTenders);

    }
    public function indexSubmittedTo(Request $request)
    {
        $generalTrait = new GeneralTrait();
        //this function will show the tenders that the company itself applied to
        //check if its the company and
        //in the request there is the token and the company id

        $order = ($request->order == "asc" || $request->order == "desc") ? $request->order : "desc";

        $id = $this->checkAndGetCompanyID($request);
        if (!is_numeric($id)) {
            // if the id is not numeric then it is a json response
            return $id;
        }

        $tendersfromDB = Tender::index()
            ->join('submit_forms', 'tenders.tender_id', '=', 'submit_forms.tender_id')
            ->where('submit_forms.company_id', '=', $id)->orderBy('tender_track.created_at', $order)
            ->get();
        return $generalTrait->returnData('tenders', $tendersfromDB);
    }
    public function indexMyTenders(Request $request)
    {
        $generalTrait = new GeneralTrait();
        //this function will show the tenders that the company itself made
        //check if its the company and maybe check if i am in the manager mode
        //in the request there is the token and the company id

        $order = ($request->order == "asc" || $request->order == "desc") ? $request->order : "desc";

        $id = $this->checkAndGetCompanyID($request);
        if (!is_numeric($id)) {
            // if the id is not numeric then it is a json response
            return $id;
        }

        $tendersfromDB = Tender::index()
            ->where('tenders.company_id', '=', $id)->orderBy('tender_track.created_at', $order)
            ->get();
        return $generalTrait->returnData('tenders', $tendersfromDB);
    }

    public function filter(Request $request)
    {

        //check the token from the request
        //and maybe need to check if the company status is tenderoffer or make the company unable to submit

        $generalTrait = new GeneralTrait();

        $category = $request->filter['category'];
        $dateFilterSpecific = $request->filter['dateFilterSpecific']; // value = date entered
        $dateFilterTenderTrack = $request->filter['dateFilterTenderTrack']['tenderTrcack'];
        $time = $request->filter['dateFilterTenderTrack']['time']; //befor or after
        $selectiveCompany = $request->filter['selective']['companies']; //one or more =>aray
        $selectiveCountry = $request->filter['selective']['countries']; // one or more => array
        $selectiveSpecialty = $request->filter['selective']['specialty']; // only one
        $open = $request->filter['open'];

        $order = ($request->order == "asc" || $request->order == "desc") ? $request->order : "desc";
        $tendersfromDB = $this->index($order, $dateFilterTenderTrack);

        if ($category) {
            $indexFilterOnCategory = $this->indexFilterOnCategory($category);
            $tendersfromDB = $tendersfromDB->intersect($indexFilterOnCategory);

        }

        if ($dateFilterTenderTrack) {
            // check if this carbon::now is not static
            $tz = $request->timeZone; // '3' for syria
            //$dateFilterSpecific = '2010-05-16'
            $date = $dateFilterSpecific ? new Carbon($dateFilterSpecific, 'UTC') : new Carbon(now('UTC'));

            $indexFilterOnDate = $this->indexFilterOnDate($date, $dateFilterTenderTrack, $time, $tz);

            $tendersfromDB = $tendersfromDB->intersect($indexFilterOnDate);
        }

        if ($selectiveCompany) {
            // tenders which been published by which companies
            $indexSelectiveCompany = $this->indexSelectiveCompany($selectiveCompany);
            $tendersfromDB = $tendersfromDB->intersect($indexSelectiveCompany);
        }
        if ($selectiveCountry) {
            $indexSelectiveCountry = $this->indexSelective('countries', $selectiveCountry);
            $tendersfromDB = $tendersfromDB->intersect($indexSelectiveCountry);
        }
        if ($selectiveSpecialty) {
            $indexSelectiveSpecialty = $this->indexSelective('specialty', $selectiveSpecialty);
            $tendersfromDB = $tendersfromDB->intersect($indexSelectiveSpecialty);
        }
        if ($open) {
            $indexOpen = $this->indexOpen($open);
            $tendersfromDB = $tendersfromDB->intersect($indexOpen);

        }

        return $generalTrait->returnData('tenders', $tendersfromDB);

    }
    public function indexFilterOnCategory($category)
    {
        $tendersfromDB = Tender::index()
            ->where('category', '=', $category)
            ->public()
            ->get();

        return $tendersfromDB;
    }

    public function indexFilterOnDate($date, $tenderTrack, $time, $tz)
    {

        // $date = date in the tz of the device
        // $tenderTrack = any date column from tender track table
        // $time = before or after

        $still = ($time == "after") ? true : false;

        $tendersfromDB = Tender::index()
            ->where($tenderTrack, $still ? '<=' : '>=', $date)
            ->public()
            ->get();

        //{{ Carbon\Carbon::parse($article->expired_at)->format('Y-m-d') }}
        //dd( $tenderTrack);

        return $tendersfromDB;
    }

    public function indexSelectiveCompany($selectiveCompany)
    {

        // $selectiveCompany => array of companies id
        $tendersfromDB = Tender::index()
            ->whereIn('tenders.company_id', $selectiveCompany)
            ->public()
            ->get();

        return $tendersfromDB;

    }

    public function indexSelective($selectiveOn, $selective)
    {
        // the value could be $selectiveOn countries,specialty
        $conditionOn = ($selectiveOn == "countries") ? 'country_id' : 'specialty';
        $tendersfromDB = Tender::index()
            ->join('selective_' . $selectiveOn, 'tenders.tender_id', '=', 'selective_' . $selectiveOn . '.tender_id')
            ->whereIn('selective_' . $selectiveOn . '.' . $conditionOn, $selective)
            ->where('tenders.selective', '==', $selectiveOn)
            ->active()
            ->get();
        return $tendersfromDB;

    }

    public function indexOpen($open)
    {
        $all = ($open == "yes") ? true : false;
        //this function will show the tenders which is open
        $tendersfromDB = Tender::index()->active()
            ->where('type', $all ? '=' : '!=', 'open')
            ->get();

        return $tendersfromDB;
    }

    public function emailsFromTender(Request $request)
    {
        // this function will give the owner of a tender all the emails of companies he had invited to the requested tender
        $generalTrait = new GeneralTrait;

        $result = $this->checkAndGetCompanyID($request);
        if (!is_numeric($result)) {
            // if the id is not numeric then it is a json response and not companyId
            return $result;
        }

        $tender_id = $request->tender_id;
        $tender = Tender::find($tender_id);
        if (!$tender) {
            return $generalTrait->returnError('401', 'this company is not found');
        }
        if($tender->company_id == $result  && $tender->selective == 'companies'){
            $fcm_tokens =  SelectiveCompany::select('fcm_tokens.fcm_token')
            ->join('companies','companies.company_id','=','selective_companies.company_id')
            ->join('users', 'users.user_id', '=', 'companies.user_id')
            ->join('fcm_tokens','fcm_tokens.user_id','=','users.user_id')
            ->where('tender_id','=',$tender_id)->get();
            return $generalTrait->returnData('fcm_tokens',$fcm_tokens);
         }else{
            return $generalTrait->returnError('401','the tender does not belong to this company or the tender is not company selective');
         }

    }
    public function notifyInvitedUsers(Request $request)
    {
        $user_id = UserAuthController::getUser($request)['user']->user_id;

        $result = MyValidator::validation($request->only('tender_id'),['tender_id'=>'required']);
        if($result['status']){
            $receivers = $this->emailsFromTender($request);
            if($receivers['status']){
                $company_name = Company::join('tenders','tenders.company_id','=','companies.company_id')
                ->where('tenders.tender_id',$request->tender_id)
                ->get('companies.company_name')->first()->company_name;
                $tender_name = Tender::where('tender_id',$request->tender_id)->get('tender_name')->first()->tender_name;
                $receivers = $receivers['fcm_tokens'];

                $data = NotificationController::getNoti($company_name,'invited you to tender: '.$tender_name,$user_id);
                if(!$data['status']){
                    return response()->json(GeneralTrait::returnError('404','couldn\'t generate notifications'));
                }
                return response()->json(GeneralTrait::returnData('notify',compact($receivers,$data)));
            }
            else response()->json($receivers);
        }
        return response()->json($result);
    }
    public function tendersInvitedTo(Request $request){
        $generalTrait = new GeneralTrait;
        $result = $this->checkAndGetCompanyID($request);
        if (!is_numeric($result)) {
            // if the id is not numeric then it is a json response and not companyId
            return $result;
        }
        $tendersInvited = Tender::index()
            ->join('selective_companies', 'selective_companies.tender_id', '=', 'tenders.tender_id')
            ->where('selective_companies.company_id', '=', $result)->get();
        return $generalTrait->returnData('tenders', $tendersInvited);

    }
    public function store(Request $request)
    {
        $res = MyValidator::validation($request->only('title','description',
        'active','type','category','selective'),[
            'title' => 'required',
            'active' => 'required',
            'category' => 'required',

        ]);
        if(!$res['status']){
            return $res;
        }
        $generalTrait = new GeneralTrait;
        $result = CompanyController::checkAndGetCompanyID($request);

        if (!is_numeric($result)) {
            // if the id is not numeric then it is a json response and not companyId
            return $result;
        }
        try {
            $tender = new Tender;
            $tender->company_id = $result;
            $tender->title = $request->title;
            $tender->description = $request->description;
            $tender->active = $request->active;
            $tender->type = $request->type;
            $tender->category = $request->category;
            
        } catch (Exception $e) {
            return $generalTrait->returnError('401', $e->getMessage());
        }
        try {
            if ($request->type == 'selective') {
                $tender->selective = $request->selective;
                if(SelectiveTenderController::validation($request)['status']){
                    $tender->save();
                    SelectiveTenderController::store($request, $tender->tender_id);
                }
            }
        } catch (Exception $e) {
            SelectiveTenderController::destroy($tender->tender_id);
            Tender::findOrFail($tender->tender_id)->delete();
            return $generalTrait->returnError('401', "couldn't save the selective " . $request->selective);
        }
        try {
            if(TenderTrackController::validation($request)['status']){
                $trackRes = TenderTrackController::store($request, $tender->tender_id);
            }
        } catch (Exception $e) {
            // delete the selective on ($request->selective) and the tender and the tender track if found
            SelectiveTenderController::destroy($tender->tender_id);
            Tender::findOrFail($tender->tender_id)->delete();
            return $generalTrait->returnError('401', "couldn't save the track of the tender... check if you entered dates");
        }
        if ($trackRes === true) {
            return $generalTrait->returnData('tender_id', $tender->tender_id, "the tender stored successfully");
        } else {
            SelectiveTenderController::destroy($tender->tender_id);
            Tender::findOrFail($tender->tender_id)->delete();
            return $trackRes;
        }

    }
    public static function showToPublic(Request $request)
    {
        // details of a tender or public view
        //validate if there is tender_id
        $generalTrait = new GeneralTrait;
        try {
            $tender = Tender::index()->addSelect('tenders.company_id','start_date', 'description', 'type', 'selective', 'category')->where('tenders.active', '=', true)->findOrFail($request->tender_id);
        } catch (Exception $e) {
            return $generalTrait->returnError('404', 'the tender you are trying to reach is not existed');
        }
        return $generalTrait->returnData('tender', $tender);
        ///+ show files

    }
    public static function showToOwner(Request $request)
    {
        // details of a tender or public view
        //validate if there is tender_id
        $generalTrait = new GeneralTrait;
        $result = CompanyController::checkAndGetCompanyID($request);
        if (!is_numeric($result)) {
            // if the id is not numeric then it is a json response and not companyId
            return $result;
        }
        try {
            $tender = Tender::index()->addSelect('start_date', 'description', 'type', 'selective', 'category', 'tender_track.judging_offers_date_end', 'tender_track.decision_committee_judgment_date_end')
                ->findOrFail($request->tender_id);
        } catch (Exception $e) {
            return $generalTrait->returnError('404', 'the tender you are trying to reach is not existed');
        }
        if ($tender->company_id != $result) {
            return $generalTrait->returnError('401', "you are not the owner of this tender ");
        }
        return $generalTrait->returnData('tender', $tender);
        ///+ show files

    }

    public function update(Request $request, $tender)
    {
        $generalTrait = new GeneralTrait;
        $result = CompanyController::checkAndGetCompanyID($request);

        $tender = Tender::findOrFail($tender);
        if (!is_numeric($result)) {
            // if the id is not numeric then it is a json response and not companyId
            return $result;
        } else if ($tender->company_id != $result) {
            return $generalTrait->returnError('401', "you are not the owner of this tender you can't edit it!");
        }
        if ($tender->active == true) {
            return $generalTrait->returnError('401', "you published this tender, you can't edit it");
        }

        try {
            $tender->update([
                'title' => $request->title,
                'description' => $request->description,
                'active' => $request->active,
                'type' => $request->type,
                'category' => $request->category,
            ]);
        } catch (Exception $e) {
            return $generalTrait->returnError('401', $e->__toString());
            //return $generalTrait->returnError('401',"couldn't save the tender");
        }
        // if request type selective delete all selective and create new 
        // if request type is open then delete all selective on
        try {
            if ($request->type == 'selective') {
                $tender->update(['selective' => $request->selective]);
                SelectiveTenderController::update($request, $tender->tender_id);
            }
        } catch (Exception $e) {
            // delete the selective on ($request->selective) and the tender if found
            return $generalTrait->returnError('401', "couldn't save the selective " . $request->selective);
        }
        try {
            $trackRes = TenderTrackController::update($request, $tender->tender_id);
        } catch (Exception $e) {
            // delete the selective on ($request->selective) and the tender and the tender track if found
            return $generalTrait->returnError('401', "couldn't save the track of the tender... check if you entered dates");
        }
        if ($trackRes === true) {

            //if front decided to accept edit on active tenders and send notification to those who submit
           /* 
            if ($tender->active) {
                $data = ['tender' => $tender->tender_id,
                         'notify' => SubmitFormController::getCompaniesfCMToken($tender->tender_id)];
                return $generalTrait->returnData('data', $data, "the tender updated successfully");
            } else
            */

            return $generalTrait->returnData('tender', $tender->tender_id, "the tender updated successfully");
        } else {
            return $trackRes;
        }

    }


}
