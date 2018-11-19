<?php
/**
 * Created by PhpStorm.
 * User: callu
 * Date: 30/10/2018
 * Time: 16:55
 */

namespace App\Utils;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Search {
    var $searchables;

    public function __construct(array $searchables) {
        $this->searchables = $searchables;
    }

    public function searchWith (array $filters){
        $results = new Collection();

        foreach ($this->searchables as $searchable) {
            $wheres = [];
            $colsFromDB = ["id"];
            $constants = [];
            foreach ($searchable["map"] as $filter => $attribute) {
                $isConstant = substr($attribute,0,1) == "!";
                if(!$isConstant) {
                    $colsFromDB[] = "{$attribute} as {$filter}";
                }else{
                    $constants[$filter] = explode("!", $attribute)[1];
                }
            }
            foreach ($filters as $filter) {
                if(array_key_exists($filter["type"], $searchable["map"])){
                    $isConstant = substr($searchable["map"][$filter["type"]],0,1) == "!";
                    $qualifyAgainst = $searchable["map"][$filter["type"]];
                    if($isConstant){
                        $qualifyAgainst = DB::raw("'".explode("!", $searchable["map"][$filter["type"]])[1]."'");
                    }
                    $wheres[] = [$qualifyAgainst, "like", $filter["content"]];
                }
            }
            $queryResults = DB::table((new $searchable["model"]())->getTable())->where($wheres)->get($colsFromDB)->map(function ($item) use ($constants){
                foreach ($constants as $constantName => $constantVal){
                    $item->$constantName = $constantVal;
                }
                return $item;
            });
            $results = $results->merge($queryResults);
        }

        return $results->all();
    }
}