<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesChannelParameter extends Model {
    protected $table = "tbl_sales_channel_parameters";

    public function salesChannel(){
        return $this->belongsToMany("App\Models\SalesChannel", "tbl_sales_channel_parameter_values")->withPivot(Schema::getColumnListing("tbl_sales_channel_parameter_values"));
    }
}
