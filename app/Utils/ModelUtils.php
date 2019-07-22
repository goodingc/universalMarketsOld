<?php
/**
 * Created by PhpStorm.
 * User: callu
 * Date: 28/11/2018
 * Time: 23:07
 */

namespace App\Utils;


class ModelUtils {
    static function populate($model, $newAttributes){
        $attributes = $model->attributesToArray();
        foreach ($attributes as $attr => $value) {
            if($attr!="id"){
                $model->{$attr} = $newAttributes[$attr]??null;
            }
        }
        $model->save();
        return $model;
    }

    static function show($model, $searchAttributes = null){
        if($searchAttributes != null){
            return $model::where($searchAttributes)->get();
        }
        return $model::all();
    }
}