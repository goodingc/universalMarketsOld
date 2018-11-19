<?php

namespace App\Http\Controllers;

use App\Models\ProductRange;
use Illuminate\Http\Request;

class ProductRangeController extends Controller {
    public function index() {
        return view("productRanges.index");
    }


    public function viewPreload(Request $request, int $id){
        return view("productRanges.index")->with("productRange", ProductRange::find($id));
    }

    public function view(Request $request, int $id){
        return view("productRanges.view")->with("productRange", ProductRange::find($id));
    }
}
