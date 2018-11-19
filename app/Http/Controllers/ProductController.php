<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller {

    public function index() {
        return view("products.index");
    }

    public function viewPreload(Request $request, int $id){
        return view("products.index")->with("product", Product::find($id));
    }

    public function view(Request $request, int $id){
        return view("products.view")->with("product", Product::find($id));
    }
}
