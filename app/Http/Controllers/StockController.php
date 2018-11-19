<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockController extends Controller {
    public function upload() {
        return view("stock.upload");
    }
}
