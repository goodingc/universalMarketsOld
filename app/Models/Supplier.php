<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\This;

class Supplier extends Model {
    protected $table = "tbl_suppliers";

    public function products(){
        return $this->belongsToMany("App\Models\Product", "tbl_product_suppliers")->withPivot(Schema::getColumnListing("tbl_product_suppliers"));
    }

    public function contacts(){
        return $this->hasMany("App\Models\SupplierContact");
    }

}