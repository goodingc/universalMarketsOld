<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBlock extends Model{
    protected $table = "tbl_blocked_products";

    public function product(){
        return $this->belongsTo("App\Models\Product");
    }

    public function reason() {
        return $this->hasOne("App\Models\ProductBlockReason", "id", "reason_id");
    }
}
