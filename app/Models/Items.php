<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Items extends Model {

    protected $fillable = [
        'title',
        'category_id',
        'stock'
    ];

    public function category() {
        return $this->belongsTo('App\Models\ItemCategories');
    }

    public function units() {
        return $this->hasMany('App\Models\ItemUnits', 'item_id', 'id');
    }

    public function stock() {
        return $this->hasMany('App\Models\StockCheck', 'item_id', 'id');
    }

    public function recipes() {
        return $this->hasMany('App\Models\RecipeItems', 'item_id', 'id');
    }
}