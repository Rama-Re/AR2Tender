<?php
namespace App\Http\Controllers\TenderRelatedControllers;

use App\Http\Controllers\Controller;
use App\Models\TenderRelated\Submit_form;
use Illuminate\Http\Request;

class SubmitFormController extends Controller
{
    public static function getTenderId(Request $request)
    {
        $tender_id = Submit_form::find($request->submit_form_id)->get('tender_id')->first();
        if(!$tender_id) return -1;
        return $tender_id->tender_id;
    }
}
