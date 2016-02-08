<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wastes extends Model {

	protected $fillable = ['type', 'item_id', 'value', 'recipe_id', 'menu_id', 'stock_period_id', 'reason_id', 'recipe_count', 'menu_count'];

    public function reason(){
        return $this->hasOne('App\Models\WasteReasons', 'id', 'reason_id');
    }

    public function item(){
        return $this->hasOne('App\Models\Items', 'id', 'item_id');
    }

    public function recipe(){
        return $this->hasOne('App\Models\Recipes', 'id', 'recipe_id');
    }

    public function menu(){
        return $this->hasOne('App\Models\Menu', 'id', 'menu_id');
    }

    public function period(){
        return $this->hasOne('App\Models\StockPeriods', 'id', 'stock_period_id');
    }

}
