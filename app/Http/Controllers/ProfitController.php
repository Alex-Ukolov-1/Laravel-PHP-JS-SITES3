<?php

namespace App\Http\Controllers;

use http\Cookie;
use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Organization;
use Auth;

class ProfitController extends Controller
{

    public function index(){
        return view('profit.index');
    }

    public function setTempDataBeforeRegister(Request $request){
        \Illuminate\Support\Facades\Cookie::queue('contract', json_encode($request->all()));
        return view('auth.register');
    }

}
