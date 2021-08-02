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

    protected static $time;

}
