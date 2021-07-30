<?php

namespace App\Http\Controllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Http\Controllers\LocWithConnectControllers\CompanyLocationController;
use App\Models\Account\Admin;
use App\Models\Account\Company;
use App\Models\LocationWithConnect\CompanyLocation;
use Illuminate\Support\Facades\Validator;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;

class CompanyController extends Controller
{    
    public static function validation(Request $request){
        $generalTrait = new GeneralTrait;
        try {
            $data = $request->only('company_name','type', 'director_name','username','image','image_path','specialty','about_us','locations');
            $validator = Validator::make($data, [
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
                //'locations.*.location_id'=> 'required|locations,location_id',
                'locations.*.branch_count'=>'required'
            ]);
            
            //Send failed response if request is not valid
            if ($validator->fails()) {
                $code = $generalTrait->returnCodeAccordingToInput($validator);
                return $generalTrait->returnValidationError($code, $validator);
            }
            else return $generalTrait->returnSuccessMessage('validated');
        } catch (\Exception $e) {
            return $generalTrait->returnError($e->getCode(), $e->getMessage());
        }
    }
    public function uploadCompanyPhoto(Request $request){
        $generalTrait = new GeneralTrait;
        try {
            $data = $request->only('image');
            $validator = Validator::make($data, [
                'image'=> 'required|mimes:png,jpg,jpeg,gif|max:2305',
            ]);
            
            //Send failed response if request is not valid
            if ($validator->fails()) {
                $code = $generalTrait->returnCodeAccordingToInput($validator);
                return $generalTrait->returnValidationError($code, $validator);
            }
        } catch (\Exception $e) {
            return $generalTrait->returnError($e->getCode(), $e->getMessage());
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
                //$company->user_id = 1;
                $company->user_id = ($response["user"])->user_id;
                $company->save();
                if($company){
                    foreach($request->locations as $location){
                        $companyLocation = new CompanyLocationController;
                        //$companyLocation->store($location,1);
                        //return $location;
                        $companyLocation->store($location,$company->company_id);
                    }
                }
                //Company created, return success response
                return $generalTrait->returnData('company',$company,'Company created successfully');
            }
            else return response()->json($result2);
        }
        else return response()->json($result);
    }
    public function getProfile(Request $request){
        $generalTrait = new GeneralTrait;
        $response = UserAuthController::validationToken($request);
        if($response["status"]){
            $result = UserAuthController::getUser($request);
            if($result["status"]){
                $company = Company::where('user_id',$result["user"]->user_id)->get();
                if (!$company) {
                    return $generalTrait->returnError('404', 'not found');
                }
                return $generalTrait->returnData('company', $company);
            }
        }
        return $response;
    }
    
    public function index()
    {
        $generalTrait = new GeneralTrait;
        $companies = Company::get();
        return $generalTrait ->returnData('companies',$companies);
    }
    
    public function getCompanyById(Request $request)
    {
        $generalTrait = new GeneralTrait;
        $company = Company::find($request->id);
        if (!$company) {
            return $generalTrait->returnError('401', 'this company is not found');
        }
        
        return $generalTrait->returnData('company', $company);
    }
    
    public function changeStatus(Request $request){
        $generalTrait = new GeneralTrait;
        Company::where('company_id',$request->id)-> update(['status' => $request -> status]);
        return $generalTrait -> returnSuccessMessage('updated');
    }
    
}
