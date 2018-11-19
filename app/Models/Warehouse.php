<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model {
    protected $table = "tbl_warehouses";

    public function inventoryBays(){
        return $this->hasMany("App\Models\InventoryBay");
    }
}
