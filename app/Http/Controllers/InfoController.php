<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Organization;
use Auth;

class InfoController extends Controller
{

    public function agreement(){
        $path = public_path('agreement.docx');

        return \response()->make(file_get_contents($path), 200, [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'inline; filename="'.'agreement.docx'.'"'
        ]);
    }

    public function license(){
        $path = public_path('license.docx');

        return \response()->make(file_get_contents($path), 200, [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'inline; filename="'.'license.docx'.'"'
        ]);
    }

    public function privacy(){
        $path = public_path('privacy.docx');

        return \response()->make(file_get_contents($path), 200, [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'inline; filename="'.'privacy.docx'.'"'
        ]);
    }

}
