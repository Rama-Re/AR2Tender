<?php

namespace App\Http\Controllers\TenderRelatedControllers;

use App\Http\Controllers\Controller;
use App\Models\TenderRelated\SelectiveCompany;
use App\Models\TenderRelated\SelectiveCountry;
use App\Models\TenderRelated\SelectiveSpecialty;
use Illuminate\Http\Request;

class SelectiveTenderController extends Controller
{
    public static function storeSelective(Request $request,$tender_id){
        //'companies','specialty','countries'
        $selective = $request->selective;
        if($selective == 'companies'){
            //  the front will give me the companyId now how ? ... they will call getAll function from CompanyController
            // and they will get all the companies with there ids when the user select an id they will put the ids in the request as an array
            $companiesIDS = $request->selective_on;
            foreach($companiesIDS as $companyID){
                $companiesSelective =  SelectiveCompany::create([
                    'company_id' => $companyID,
                    'tender_id' => $tender_id
                ]);
            }
        }else if($selective == 'specialty'){
            $specialtySelective = SelectiveSpecialty::create([
                'specialty' => $request->selective_on,
                'tender_id' => $tender_id
            ]);
        }else if ($selective == 'countries'){
            //  the front will give me the countryID now how ? ... they will call getAllAsJSON function from CountryController
            // and they will get all the companies with there ids when the user select an id they will put the ids in the request as an array
            $countriesIDS = $request->selective_on;
            foreach($countriesIDS as $countryID){
                $countriesSelective =  SelectiveCountry::create([
                    'country_id' => $countryID,
                    'tender_id' => $tender_id
                ]);
            }
        }

    }
}
