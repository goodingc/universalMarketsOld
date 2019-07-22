<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierContact extends Model {
    protected $table = "tbl_supplier_contacts";

    protected $guarded = ["id"];

    public function supplier(){
        return $this->belongsTo("App\Models\Supplier");
    }
}
