<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBarcode extends Model {
    protected $table = "tbl_product_barcodes";

    public function product(){
        return $this->belongsTo("App\Models\Product");
    }
}
