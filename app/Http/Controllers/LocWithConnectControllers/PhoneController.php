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

class PhoneController extends Controller
{
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
    public function store($phone_number,$company_location_id)
    {
        $generalTrait = new GeneralTrait;
        $phone = new Phone;
        $phone->phone_number = $phone_number;
        $phone->company_location_id = $company_location_id;
        return $generalTrait->returnData('phone',$phone,'phone added successfuly');
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
