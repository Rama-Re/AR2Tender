<?php

namespace App\Http\Controllers\LocWithConnectContollers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LocationWithController\Country;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class CompanyController extends Controller
{
    public function index(){
        $countries = Country::get();
        return response()->json($countries);
    }
}
