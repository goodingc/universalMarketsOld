<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ProductAttribute extends Model {
    protected $table = "tbl_product_attributes";

    protected $fillable = ["product_attribute_id"];

    public function products(){
        return $this->belongsToMany("App\Models\Product", "tbl_product_attribute_values")->withPivot(Schema::getColumnListing("tbl_product_attribute_values"));
    }
}
