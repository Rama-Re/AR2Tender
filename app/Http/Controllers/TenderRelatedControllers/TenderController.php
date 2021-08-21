<?php


namespace App\Http\Controllers\TenderRelatedControllers;

use App\Http\Controllers\AccountControllers\CompanyController;
use App\Http\Controllers\AccountControllers\UserAuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Models\TenderRelated\SelectiveCompany;
use App\Models\TenderRelated\Tender;
use App\Models\TenderRelated\Tender_track;
use App\Models\User;
use Carbon\Carbon;
use Error;
use ErrorException;
use Exception;
use Hamcrest\Type\IsNumeric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;

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

    public function index($order,$dateFilterTenderTrack)
    {
        //check the token from the request
        $tendersfromDB = Tender::index()
            ->public()
            ->orderby($dateFilterTenderTrack?$dateFilterTenderTrack:'tender_track.created_at',$order)
            ->get(); // order by latest

        return  $tendersfromDB;
        
    }

    public function indexSearch(Request $request){

        $generalTrait = new GeneralTrait();
        //this function will show the tenders that the company itself applied to
        //check if its the company and
        //in the request there is the token and the company id
        $search = $request->search;

        $tendersfromDB = Tender::index()
        ->public()
        ->where('Title', 'like', '%'.$search.'%')
        ->orWhere('company_name', 'like', '%'.$search.'%')
        ->get();

        $sortedTenders = $tendersfromDB->sortBy(function ($tender,$key) use ($search) {
            $rest = strlen($tender->Title)-strlen($search);
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
        if(!is_numeric($id)){
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
        if(!is_numeric($id)){
            // if the id is not numeric then it is a json response
            return $id;
        }
        
        $tendersfromDB = Tender::index()
            ->where('tenders.company_id', '=', $id)->orderBy('tender_track.created_at', $order)
            ->get();
        return $generalTrait->returnData('tenders', $tendersfromDB);
    }


    public function filter(Request $request){

        //check the token from the request
        //and maybe need to check if the company status is tenderoffer or make the company unable to submit
        
        $generalTrait = new GeneralTrait();

        $category = $request->filter['category'];
        $dateFilterSpecific = $request->filter['dateFilterSpecific']; // value = date entered
        $dateFilterTenderTrack = $request->filter['dateFilterTenderTrack']['tenderTrcack'];
        $time= $request->filter['dateFilterTenderTrack']['time'];//befor or after
        $selectiveCompany = $request->filter['selective']['companies']; //one or more =>aray
        $selectiveCountry = $request->filter['selective']['countries']; // one or more => array
        $selectiveSpecialty = $request->filter['selective']['specialty']; // only one
        $open = $request->filter['open'];
        
        
        $order = ($request->order == "asc" || $request->order == "desc") ? $request->order : "desc";
        $tendersfromDB = $this->index($order,$dateFilterTenderTrack);
        
        if ($category) {
            $indexFilterOnCategory = $this->indexFilterOnCategory($category);
            $tendersfromDB = $tendersfromDB->intersect($indexFilterOnCategory);
            
        }

        if ($dateFilterTenderTrack) {
            // check if this carbon::now is not static
            $tz = $request->timeZone; // '3' for syria 
            //$dateFilterSpecific = '2010-05-16'
            $date = $dateFilterSpecific?new Carbon($dateFilterSpecific,'UTC'):new Carbon(now('UTC'));
            
            $indexFilterOnDate = $this->indexFilterOnDate($date,$dateFilterTenderTrack,$time,$tz);
            
            $tendersfromDB = $tendersfromDB->intersect($indexFilterOnDate);
        }

        if ($selectiveCompany) {
            // tenders which been published by which companies
            $indexSelectiveCompany = $this->indexSelectiveCompany($selectiveCompany);
            $tendersfromDB = $tendersfromDB->intersect($indexSelectiveCompany);
        }
        if ($selectiveCountry) {
            $indexSelectiveCountry = $this->indexSelective('countries',$selectiveCountry);
            $tendersfromDB = $tendersfromDB->intersect($indexSelectiveCountry);
        }
        if ($selectiveSpecialty) {
            $indexSelectiveSpecialty = $this->indexSelective('specialty',$selectiveSpecialty);
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

    public function indexFilterOnDate($date,$tenderTrack,$time,$tz)
    {
        

        // $date = date in the tz of the device
        // $tenderTrack = any date column from tender track table
        // $time = before or after

        $still = ($time=="after")?true:false;

            $tendersfromDB = Tender::index()
            ->where($tenderTrack, $still ? '<=' : '>=', $date)
            ->public()
            ->get();
        
        //{{ Carbon\Carbon::parse($article->expired_at)->format('Y-m-d') }}
        //dd( $tenderTrack);

        return $tendersfromDB;
    }

    public function indexSelectiveCompany($selectiveCompany){

        // $selectiveCompany => array of companies id
        $tendersfromDB = Tender::index()
        ->whereIn('tenders.company_id', $selectiveCompany)
        ->public()
        ->get();

        return $tendersfromDB;

    }

    public function indexSelective($selectiveOn,$selective)
    {
        // the value could be $selectiveOn countries,specialty
        $conditionOn = ($selectiveOn=="countries")?'country_id':'specialty';
        $tendersfromDB = Tender::index()
            ->join('selective_' . $selectiveOn, 'tenders.tender_id', '=', 'selective_' . $selectiveOn . '.tender_id')
            ->whereIn('selective_' . $selectiveOn .'.'.$conditionOn, $selective)
            ->where('tenders.selective', '==', $selectiveOn)
            ->active()
            ->get();
        return  $tendersfromDB;

    }

    public function indexOpen($open)
    {
        $all = ($open=="yes")?true:false;
        //this function will show the tenders which is open
        $tendersfromDB = Tender::index()->active()
            ->where('type',$all? '=':'!=', 'open')
            ->get();

       
        return $tendersfromDB;
    }

    function checkAndGetCompanyID (Request $request){
        $generalTrait = new GeneralTrait();
        
        $user = UserAuthController::getUser($request);
        try{
            $id = $user["user"]->user_id;
            //dd($id); //5
            $id = CompanyController::getCompanyId($id);
            // if the value is not numeric then the type is not company
            ///dd($id); //4
            if(!is_numeric($id)){
                return $generalTrait->returnError('403',"the account is not a company account");
            }else{
                return $id;
            }
        }catch(ErrorException $e){
           return $generalTrait->returnError('404',"not logged in");
        }
    }

    public function emailsFromTender (Request $request){
        // this function will give the owner of a tender all the emails of companies he had invited to the requested tender 
        $generalTrait = new GeneralTrait;

        $result = $this->checkAndGetCompanyID($request);
        if(!is_numeric($result)){
            // if the id is not numeric then it is a json response and not companyId
            return $result;
        }

        $tender_id = $request->tender_id;
        $tender= Tender::find($tender_id);
        if (!$tender) {
            return response()->json($generalTrait->returnError('401', 'this company is not found'));
        }
        if($tender->company_id == $result  && $tender->selective == 'companies'){
           $companiesEmail =  SelectiveCompany::select('email')
           ->join('companies','companies.company_id','=','selective_companies.company_id')
           ->join('users', 'users.user_id', '=', 'companies.user_id')
           ->where('tender_id','=',$tender_id)->get();
           return $generalTrait->returnData('emails',$companiesEmail);
        }else{
           return $generalTrait->returnError('401','the tender does not belong to this company or the tender is not company selective');
        }

    }
    public function tendersInvitedTo(Request $request){
        $generalTrait = new GeneralTrait;
        $result = $this->checkAndGetCompanyID($request);
        if(!is_numeric($result)){
            // if the id is not numeric then it is a json response and not companyId
            return $result;
        }
        $tendersInvited =  Tender::index()
           ->join('selective_companies','selective_companies.tender_id','=','tenders.tender_id')
           ->where('selective_companies.company_id','=',$result)->get();
        return $generalTrait->returnData('tenders',$tendersInvited);

    }


}
