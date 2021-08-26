<?php

namespace App\Http\Controllers\LocWithConnectControllers;

use App\Http\Controllers\Controller;

use App\Http\Controllers\GeneralTrait;
use App\Http\Controllers\MyValidator;
use App\Models\LocationWithConnect\CompanyLocation;
use App\Models\LocationWithConnect\Location;
use App\Models\LocationWithConnect\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use League\Flysystem\Adapter\Local;

class CompanyLocationController extends Controller
{
    public static function validation(Request $request){
        $data = $request->only('location_id','branch_count');
        $rules =  [
            'location_id' => 'required|locations,location_id',
            'branch_count' => 'required'
        ];
        return MyValidator::validation($data, $rules);
    }
    public function deleteCompanyLocations($company_id)
    {
        CompanyLocation::where('company_id',$company_id)->delete();
    }
    public function store($request,$company_id)
    {
        $generalTrait = new GeneralTrait;
        $companyLocation = new CompanyLocation;
        $companyLocation->location_id = $request['location_id'];
        $companyLocation->company_id = $company_id;
        $companyLocation->branch_count = $request['branch_count'];
        $companyLocation->save();
        foreach($request['phones'] as $phone){
            $phoneController = new PhoneController;
            $phoneController->store($phone,$companyLocation->company_location_id);
        }
        return $generalTrait->returnData('location',$companyLocation,'companyLocation added successfuly');
        
    }
}
