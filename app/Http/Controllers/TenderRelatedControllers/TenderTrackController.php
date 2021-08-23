<?php

namespace App\Http\Controllers\TenderRelatedControllers;

use App\Http\Controllers\Controller;
use App\Models\TenderRelated\Tender_track;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

use function PHPUnit\Framework\returnSelf;

class TenderTrackController extends Controller
{
    public static function store(Request $request,$tenderID)
    {
        
            $date = new Carbon(now('UTC'));
            $tender_track = new Tender_track();
            $tender_track->tender_id = $tenderID;
            $tender_track->start_date = ($request->start_date)?$request->start_date:$date;
            $tender_track->end_date = ($request->end_date)?$request->end_date:$date->addMonths(3); // 3 months
            $tender_track->judging_offers_date = ($request->judging_offers_date)?$request->judging_offers_date:$date->addMonths(4);
            $tender_track->judging_offers_by_administrator_date = ($request->judging_offers_by_administrator_date)?$request->judging_offers_by_administrator_date:$date->addDays(127);
            $tender_track->decision_committee_judgment_date = ($request->decision_committee_judgment_date)?$request->decision_committee_judgment_date:$date->addMonths(5);
            $tender_track->administrator_decision_committee_judgment_date = ($request->administrator_decision_committee_judgment_date)?$request->administrator_decision_committee_judgment_date:$date->addDays(157);
            $tender_track->announcing_result_date = ($request->announcing_result_date)?$request->announcing_result_date:$date->addDays(6);
            $tender_track->save();
    }
  
}
