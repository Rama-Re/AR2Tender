<?php

namespace App\Http\Controllers\AccountControllers;
use Illuminate\Http\Request;
use App\Models\Account\Company;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class CompanyController extends Controller
{
    function create(Request $request){
        
        $request->validate([
            'username'=>'required|username|unique:companies',
        ]);

        $Company = new Company;
        $Company->company_name = $request->company_name;
        $Company->director_name = $request->director_name;
        $Company->username = $request->username;
        $Company->about_us = $request->about_us;
        $query = $Company->save();

        if($query){
            return ['Result'=>'success','company_id' => $Company->id];
        }else{
            return ['Result'=>'failed','message' => 'this username already used, try another one'];
        }
    }

}
