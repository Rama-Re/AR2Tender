<?php

namespace App\Http\Controllers\TenderRelatedControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Models\TenderRelated\Tender;
use App\Models\TenderRelated\Tender_track;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TenderController extends Controller
{
    //use GeneralTrait;
    //get all tenders 
    public function index(Request $request){
        //check the token from the request
        $generalTrait = new GeneralTrait;
        $tendersfromDB = Tender::select('tenders.tender_id','end_date','Title','company_name','image')
        ->join('tender_track', 'tender_track.tender_id', '=', 'tenders.tender_id')
        ->latest('tender_track.created_at')->get();// order by latest
        return $generalTrait->returnData('tenders', $tendersfromDB);
    }


    public function indexFilterOnDate(Request $request){
        $generalTrait = new GeneralTrait;

        //$still = true/false
        // $date = any date column from tender track table


        $date = $request->only('date');
        $still = $request->only('still');
        
        //check the token from the request
        //and maybe need to check if the company status is tenderoffer or make the company unable to submit
        // check if this carbon::now is not static 
        $tendersfromDB = Tender::select('tenders.tender_id','end_date','Title','company_name','image','')
            ->join('tender_track', 'tender_track.tender_id', '=', 'tenders.tender_id')
            ->join('companies','tenders.company_id','=','companies.company_id')
            ->where($date,$still?'>=':'<=',Carbon::now())->latest($date)
            ->get();

        return $generalTrait->returnData('tenders', $tendersfromDB);
    }

    public function indexFilterOnCategory(Request $request){
        $generalTrait = new GeneralTrait();

        $category = $request->only('category');

        //check the token from the request
        //and maybe need to check if the company status is tenderoffer or make the company unable to submit
        
        $tendersfromDB = Tender::select('tenders.tender_id','end_date','Title','company_name','image')
            ->join('tender_track', 'tender_track.tender_id', '=', 'tenders.tender_id')
            ->join('companies','tenders.company_id','=','companies.company_id')
            ->where('category','=',$category)->latest('tender_track.created_at')
            ->get();

            if(!$tendersfromDB){
                return $this->returnError('404','not found');
            }

            return $generalTrait->returnData('tenders',$tendersfromDB);
       
    }


    
}
