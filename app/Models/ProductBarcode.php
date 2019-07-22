<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBarcode extends Model {
    protected $table = "tbl_product_barcodes";

    protected $casts = ["id" => "string"];

    protected $guarded = [];

    protected $keyType = "string";

    public $incrementing = false;

    public function product(){
        return $this->belongsTo("App\Models\Product");
    }
}
