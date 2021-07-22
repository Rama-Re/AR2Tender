<?php

namespace App\Http\Controllers\LocWithConnectContollers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LocationWithController\Location;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class CompanyController extends Controller
{
    public function index($id){
        $locations = Location::get()->where();
        return response()->json($locations);
    }
}
