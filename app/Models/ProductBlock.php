<?php

namespace App\Models;

use App\Traits\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Model;

class ProductBlock extends Model{
    use HasCompositePrimaryKey;

    protected $table = "tbl_blocked_products";

    protected $guarded = [];

    protected $primaryKey = ["product_id", "reason_id", "sales_channel_id"];

    public function product(){
        return $this->belongsTo("App\Models\Product");
    }

    public function reason() {
        return $this->hasOne("App\Models\ProductBlockReason", "id", "reason_id");
    }
}
