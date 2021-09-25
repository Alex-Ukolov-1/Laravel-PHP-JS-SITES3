<?php

namespace App\Http\Controllers;

class StartController extends Controller
{
    protected $title = 'Старт';

    public function index()
    {
        return view('start.index', [
            'title'   => $this->title
        ]);
    }
}
