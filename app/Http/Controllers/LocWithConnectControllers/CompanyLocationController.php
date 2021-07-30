<?php

namespace App\Http\Controllers\LocWithConnectControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use App\Models\LocationWithConnect\CompanyLocation;
use App\Models\LocationWithConnect\Location;
use App\Models\LocationWithConnect\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use League\Flysystem\Adapter\Local;

class CompanyLocationController extends Controller
{
    public static function validation(Request $request){
        $generalTrait = new GeneralTrait;
        try {
                $data = $request->only('location_id','branch_count');
                $validator = Validator::make($data, [
                    'location_id' => 'required|locations,location_id',
                    'branch_count' => 'required'
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CompanyLocation  $companyLocation
     * @return \Illuminate\Http\Response
     */
    public function show(CompanyLocation $companyLocation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CompanyLocation  $companyLocation
     * @return \Illuminate\Http\Response
     */
    public function edit(CompanyLocation $companyLocation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CompanyLocation  $companyLocation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompanyLocation $companyLocation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CompanyLocation  $companyLocation
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompanyLocation $companyLocation)
    {
        //
    }
}
