<?php

namespace App\Http\Controllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Http\Controllers\LocWithConnectControllers\CompanyLocationController;
use App\Http\Controllers\MyValidator;
use App\Models\Account\Admin;
use App\Models\Account\Company;
use App\Models\LocationWithConnect\CompanyLocation;
use App\Models\LocationWithConnect\Country;
use App\Models\LocationWithConnect\Location;
use App\Models\LocationWithConnect\Phone;
use Illuminate\Support\Facades\Validator;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CompanyController extends Controller
{    
    public static function validation(Request $request){
        return MyValidator::validation($request->only('company_name','type', 'director_name','username','image','image_path','specialty','about_us','locations'),
         [
            'type' => 'required|in:company',
                'company_name' => 'required|string',
                'director_name' => 'required|string',
                'username' => 'required|string',
                'image'=> 'required|string',
                'image_path' => 'required|string',
                'specialty' => 'required|in:medical,engineering-related,Raw-materials,technical,technology-related,Other',
                'status' => 'in:TenderOffer,TendersManager',
                'about_us' => 'required',
                'locations' => 'required|array',
                'locations.*.location_id'=> 'required',
                'locations.*.branch_count'=>'required'
        ]);
    }
    public function uploadCompanyPhoto(Request $request){
        $generalTrait = new GeneralTrait;
        $result = MyValidator::validation($request->only('image'),[
            'image'=> 'required|mimes:png,jpg,jpeg,gif|max:2305',
        ]);
        if(!$result['status']){
            return $result;
        }
        
        if($file = $request->file('image'))
        { 
            $image_path = $file->store('public/images');
            $name = $file->getClientOriginalName();
            $image = time().$name;
            return $generalTrait->returnData('image_details',compact('image','image_path'));
        }
        else return $generalTrait->returnError('401', 'can\'t upload image');
        
    }
    public function register(Request $request){
        $generalTrait = new GeneralTrait;
        $userC = new UserAuthController;
        $result = UserAuthController::validation($request);
        if($result["status"]){
            $result2 = $this->validation($request);
            if($result2["status"]){
                $response = $userC->register($request);
                $company = new Company;
                $company->company_name = $request->company_name;
                $company->director_name = $request->director_name;
                $company->username = $request->username;
                $company->specialty = $request->specialty;
                $company->status = $request->status;
                $company->about_us = $request->about_us;
                $company->image = $request->image;
                $company->image_path = $request->image_path;
                $company->user_id = 1;
                $company->user_id = ($response["user"])->user_id;
                $company->save();
                if($company){
                    foreach($request->locations as $location){
                        $companyLocation = new CompanyLocationController;
                        $companyLocation->store($location,$company->company_id);
                    }
                }
                //Company created, return success response
                return response()->json($generalTrait->returnData('company',$company,'Company created successfully'));
            }
            else return response()->json($result2);
        }
        else return response()->json($result);
    }
    public static function getUserID(Request $request){
        $generalTrait = new GeneralTrait;
        $user_id = Company::find($request->company_id)->get('user_id');
        return $generalTrait ->returnData('user_id',$user_id);
    }
    public function getAll(){
        $generalTrait = new GeneralTrait;
        $companies = Company::get();
        return response()->json($generalTrait ->returnData('companies',$companies));
    }

    public function getProfile(Request $request){
        $generalTrait = new GeneralTrait;
        $response = UserAuthController::validationToken($request);
        if($response["status"]){
            $result = UserAuthController::getUser($request);
            if($result["status"]){
                $company = Company::where('user_id',$result["user"]->user_id)->get()->first();
                $locationsID = Company::join('company_locations', 'company_locations.company_id', '=', 'companies.company_id')
                ->where('companies.company_id',$company->company_id)
                ->get(['company_locations.location_id','company_locations.company_location_id','company_locations.branch_count']);
                $locations = array();
                $count = 0;
                foreach($locationsID as $branch){
                    $phone_numbers = Phone::join('company_locations','company_locations.company_location_id','phones.company_location_id')
                    ->where('company_locations.company_location_id',$branch->company_location_id)
                    ->get('phone_number');
                    $i=0;
                    $phones = array();
                    foreach($phone_numbers as $phone){
                        $phones[$i] = $phone->phone_number;
                        $i++;
                    }
                    $location = Location:: where('location_id',$branch->location_id)->get('location_name')->first()->location_name;
                    $country_id = Location:: where('location_id',$branch->location_id)->get('country_id')->first()->country_id;
                    $country = Country::where('country_id',$country_id)->get('country_name')->first()->country_name;
                    $locations[$count] = compact('location','country','phones');
                    $count++;
                }
                return compact('company','locations');
                $company = Company::where('user_id',$result["user"]->user_id)->get();
                if (!$company) {
                    return response()->json($generalTrait->returnError('404', 'not found'));
                }
                return response()->json($generalTrait->returnData('Profile',compact('company','locations'),'Success'));
            }
            else response()->json($result);
        }
        return response()->json($response);
    }
    
    public function getCompanyById(Request $request)
    {
        $generalTrait = new GeneralTrait;
        $company = Company::find($request->company_id);
        if (!$company) {
            return response()->json($generalTrait->returnError('401', 'this company is not found'));
        }
        
        return response()->json($generalTrait->returnData('company', $company));
    }
    
    public function changeStatus(Request $request){
        $generalTrait = new GeneralTrait;
        $company = Company::find($request->company_id);
        if(Company::find($request->company_id)->get('status')->first()->status == 'TenderOffer')
        {
            $company->status = 'TendersManager';
            $company->save();
            return response()->json($generalTrait -> returnSuccessMessage('updated'));
        }
        else if(Company::find($request->company_id)->get('status')->first()->status == 'TendersManager')
        {
            $company->status = 'TenderOffer';
            $company->save();
            return response()->json($generalTrait -> returnSuccessMessage('updated'));
        }
        return response()->json($generalTrait -> returnError('403','something went wrong'));
    }
    
}
