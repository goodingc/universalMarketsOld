<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SalesChannel extends Model {
    protected $table = "tbl_sales_channels";

    public function products(){
        return $this->belongsToMany("App\Models\Product", "tbl_product_sales_channels")->withPivot(Schema::getColumnListing("tbl_product_sales_channels"));
    }

    public function availableChannel(){
        return $this->belongsTo("App\Models\AvailableChannel");
    }

    public function parameters(){
        return $this->belongsToMany("App\Models\SalesChannelParameter", "tbl_sales_channel_parameter_values")->withPivot(Schema::getColumnListing("tbl_sales_channel_parameter_values"));
    }

    public function parameter(string $key){
        if($entry = $this->parameters->where("name", "=" , $key)->first()){
            return $entry->pivot->value;
        }
        return null;
    }
}
