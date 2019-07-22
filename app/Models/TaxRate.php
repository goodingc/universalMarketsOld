<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model {
    protected $table = "tbl_tax_rates";

    protected $guarded = ["id"];

    public function products(){
        return $this->hasMany("App\Models\Product");
    }
}
