<?php

namespace App\Http\Controllers\TenderRelatedControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Models\TenderRelated\Tender_track;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

use function PHPUnit\Framework\returnSelf;

class TenderTrackController extends Controller
{
    public static function store(Request $request,$tenderID)
    {
        //$date = $dateFilterSpecific?new Carbon($request->start_date,'UTC'):new Carbon(now('UTC'));
            
        // change the dates and check dates
        $generalTrait = new GeneralTrait;
            $date = new Carbon(now('UTC'));
            $tender_track = new Tender_track();
            $tender_track->tender_id = $tenderID;
            $tender_track->start_date = ($request->has('start_date'))?new Carbon($request->start_date,'UTC'):$date;

            if($request->end_date){
                $endDate = new Carbon($request->end_date,'UTC');
                if($endDate > $tender_track->start_date){
                    $tender_track->end_date =  $endDate;
                }else{
                    return $generalTrait->returnError('401',"the end date must be before the start date");
                }
            }else{
                $tender_track->end_date = $tender_track->start_date->copy()->addMonths(3);
            }

            if($request->judging_offers_date_end){
                $judging_offers_date_end = new Carbon($request->judging_offers_date_end,'UTC');
                if($judging_offers_date_end >  $tender_track->end_date){
                    $tender_track->judging_offers_date_end =  $judging_offers_date_end;
                }else{
                    return $generalTrait->returnError('401',"the end of judging offers date must be after the end date");
                }
            }else{
                $tender_track->judging_offers_date_end  =  $tender_track->end_date->copy()->addMonth();
            }

            if($request->decision_committee_judgment_date_end){
                $decision_committee_judgment_date_end = new Carbon($request->decision_committee_judgment_date_end,'UTC');
                if($decision_committee_judgment_date_end >  $tender_track->judging_offers_date_end){
                    $tender_track->decision_committee_judgment_date_end =  $decision_committee_judgment_date_end;
                }else{
                    return $generalTrait->returnError('401',"the end of decision committee judgment date must be after the end of judging offers date ");
                }
            }else{
                $tender_track->decision_committee_judgment_date_end  = $tender_track->judging_offers_date_end->copy()->addMonth();
            }
            
            $tender_track->save();
            return true;
    }
    public static function checkBeforEnd ($date,$tender_id){
        $endDateOFTender =Tender_track::where('tender_id', $tender_id)->value('end_date');
        if( $date <$endDateOFTender ){
            return true;
        }else{
            return false;
        }
    }
    public static function checkAfterStart($date,$tender_id)
    {
        $startDateOFTender =Tender_track::where('tender_id', $tender_id)->value('start_date');
        if( $date > $startDateOFTender ){
            return true;
        }else{
            return false;
        }
        
    }
    public static function update(Request $request,$tender_id){

    }
}
