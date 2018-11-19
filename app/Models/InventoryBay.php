<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryBay extends Model {
    protected $table = "tbl_inventory_bays";

    public function products(){
        return $this->belongsToMany("App\Models\Product", "tbl_product_inventory_bays")->withPivot(Schema::getColumnListing("tbl_product_inventory_bays"));
    }

    public function warehouse(){
        return $this->belongsTo("App\Models\Warehouse");
    }
}
