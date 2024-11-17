<?php

namespace App\Http\Controllers;

class HomeController extends _BaseController
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // return view('pages/home');
        return view('pages/home');
    }
}
