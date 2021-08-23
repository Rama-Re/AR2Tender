<?php

namespace App\Models\TenderRelated;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tender_track extends Model
{
    use HasFactory;
    protected $table = 'tender_track';
    protected $primaryKey = 'tender_track_id';

    protected $dates = ['start_date','end_date','judging_offers_date'
    ,'judging_offers_by_administrator_date','decision_committee_judgment_date',
    'administrator_decision_committee_judgment_date','announcing_result_date'];


}
