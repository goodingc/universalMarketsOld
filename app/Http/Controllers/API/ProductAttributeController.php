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

    function get(Request $request, int $id){
        $productAttribute = ProductAttribute::find($id);
        return $productAttribute;
    }

    public function create(Request $request){
        $productAttribute = ProductAttribute::create();
        return $productAttribute;
    }

    public function edit(Request $request, int $id){
        $productAttribute = ProductAttribute::find($id);
        $data = $request->get("data");
        $attributes = $productAttribute->attributesToArray();
        foreach ($attributes as $attr => $value) {
            if(isset($productAttribute->{$attr}) && $attr!="id"){
                $productAttribute->{$attr} = $data[$attr];
            }

        }
        $productAttribute->save();
        return $this->get($request, $id);
    }

    public function destroy(Request $request, int $id){
        ProductAttribute::destroy($id);
    }
}
