<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryBay extends Model {
    protected $table = "tbl_inventory_bays";

    protected $guarded = ["id"];

    public function inventoryBayAssignments() {
        return $this->hasMany("App\Models\InventoryBayAssignment");
    }

    public function warehouse(){
        return $this->belongsTo("App\Models\Warehouse");
    }
}
