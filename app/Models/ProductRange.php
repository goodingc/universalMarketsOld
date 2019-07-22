<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRange extends Model {
    protected $table = "tbl_product_ranges";

    protected $guarded = ["id"];

    public function products(){
        return $this->hasMany("App\Models\Product");
    }
}
