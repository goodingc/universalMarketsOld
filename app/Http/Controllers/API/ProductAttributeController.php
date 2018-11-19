<?php

namespace App\Http\Controllers\API;

use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductAttributeController extends Controller {

    function show(Request $request){
        $productAttributes = ProductAttribute::all();
        return $productAttributes;
    }

    public function create(Request $request){
        $productAttribute = ProductAttribute::create();
        return $productAttribute;
    }
}
