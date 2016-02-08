<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Items extends Model {

    protected $fillable = [
        'title',
        'category_id',
        'stock',
        'price'
    ];

    public function category() {
        return $this->belongsTo('App\Models\ItemCategories');
    }

    public function units() {
        return $this->hasMany('App\Models\ItemUnits', 'item_id', 'id');
    }

    public function stockCheck() {
        return $this->hasMany('App\Models\StockCheck', 'item_id', 'id');
    }

    public function stock() {
        return$this->hasMany('App\Models\StockItem', 'item_id', 'id');
    }

    public function recipes() {
        return $this->hasMany('App\Models\RecipeItems', 'item_id', 'id');
    }

    public function purchases(){
        return $this->hasMany('App\Models\ItemPurchases', 'item_id', 'id');
    }

    public function menus(){
        return $this->hasMany('App\Models\Menu', 'item_id', 'id');
    }

    public function wastes(){
        return $this->hasMany('App\Models\Wastes', 'item_id', 'id');
    }
}
