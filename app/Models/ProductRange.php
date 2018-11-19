<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRange extends Model {
    protected $table = "tbl_product_ranges";

    public function products(){
        return $this->hasMany("App\Models\Product");
    }
}
