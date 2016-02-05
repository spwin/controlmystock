<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model {

	protected $fillable = ['number', 'title', 'price', 'type', 'item_id', 'recipe_id', 'value', 'checked'];

    public function item(){
        return $this->hasOne('App\Models\Items', 'id', 'item_id');
    }

    public function recipe(){
        return $this->hasOne('App\Models\Recipes', 'id', 'recipe_id');
    }

    public function sales(){
        return $this->hasMany('App\Models\SaleItems', 'menu_id', 'id');
    }

    public function wastes(){
        return $this->hasMany('App\Models\Wastes', 'menu_id', 'id');
    }
}
