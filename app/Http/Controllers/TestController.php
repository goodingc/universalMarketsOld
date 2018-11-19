<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        set_time_limit(0);
        return view('tests');
    }
}
