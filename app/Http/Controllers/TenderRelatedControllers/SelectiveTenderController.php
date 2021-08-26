<?php

namespace App\Http\Controllers\TenderRelatedControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MyValidator;
use App\Models\Account\Company;
use App\Models\LocationWithConnect\Location;
use App\Models\TenderRelated\SelectiveCompany;
use App\Models\TenderRelated\SelectiveCountry;
use App\Models\TenderRelated\SelectiveSpecialty;
use App\Models\TenderRelated\Tender;
use Exception;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Catch_;

class SelectiveTenderController extends Controller
{
    public static function store(Request $request, $tender_id)
    {
        //'companies','specialty','countries'
        $res = MyValidator::validation($request->only('selective','selective_on'),[
            'selective' => 'required',
            'selective_on' => 'required',
        ]);
        if(!$res['status']){
            return $res;
        }
        $selective = $request->selective;
        if ($selective == 'companies') {
            //  the front will give me the companyId now how ? ... they will call getAll function from CompanyController
            // and they will get all the companies with there ids when the user select an id they will put the ids in the request as an array
            $companiesIDS = $request->selective_on;
            
            foreach ($companiesIDS as $companyID) {
                $companiesSelective = new SelectiveCompany;
                $companiesSelective->company_id = $companyID;
                $companiesSelective->tender_id = $tender_id;
                $companiesSelective->save();
            }
        } else if ($selective == 'specialty') {
            $specialtySelective = new SelectiveSpecialty;
            $specialtySelective->specialty = $request->selective_on[0];
            $specialtySelective->tender_id = $tender_id;
            $specialtySelective->save();

        } else if ($selective == 'countries') {
            //  the front will give me the countryID now how ? ... they will call getAllAsJSON function from CountryController
            // and they will get all the companies with there ids when the user select an id they will put the ids in the request as an array
            $countriesIDS = $request->selective_on;
                foreach ($countriesIDS as $countryID) {
                    $countriesSelective = new SelectiveCountry;
                    $countriesSelective->country_id = $countryID;
                    $countriesSelective->tender_id = $tender_id;
                    $countriesSelective->save();
                }
            
        }

    }
    public static function checkAbility($company_id, $tender_id)
    {

        $tenderType = Tender::find($tender_id)->value('type');
        if ($tenderType == 'open') {
            return true;
        } else {
            $tender_selective = Tender::find($tender_id)->value('selective');
            if ($tender_selective == 'companies') {
                if (SelectiveCompany::where('company_id', $company_id)->where('tender_id', $tender_id)->exists()) {
                    return true;
                }
            } else if ($tender_selective == 'specialty') {
                $companySpecialty = Company::find($company_id)->value('specialty');
                if ($companySpecialty == SelectiveSpecialty::where('tender_id', $tender_id)->value('specialty')) {
                    return true;
                }
            } else if ($tender_selective == 'countries') {
                $companyCountriesID = Location::join('company_locations', 'company_locations.location_id', '=', 'locations.location_id')
                    ->where('company_id', $company_id)->pluck('country_id');
                $requiredCountriesID = SelectiveCountry::where('tender_id', $tender_id)->pluck('country_id');
                $result = $companyCountriesID->intersect($requiredCountriesID);
                if ($result->isNotEmpty()) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function destroy($tender_id)
    {
        SelectiveCompany::where('tender_id', $tender_id)->delete();
        SelectiveCountry::where('tender_id', $tender_id)->delete();
        SelectiveSpecialty::where('tender_id', $tender_id)->delete();
    }
    public static function update($request, $tender_id)
    {
        self::destroy($request, $tender_id);
        self::store($request, $tender_id);
    }
}
