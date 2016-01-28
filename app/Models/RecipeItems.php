<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeItems extends Model {

    protected $fillable = [
        'recipe_id',
        'item_id',
        'wastage',
        'value',
        'type',
        'sub_recipe'
    ];

    public function item() {
        return $this->hasOne('App\Models\Items', 'id', 'item_id');
    }

    public function recipe() {
        return $this->hasone('App\Models\Recipes', 'id', 'recipe_id');
    }

    public function subrecipe(){
        return $this->hasOne('App\Models\Recipes', 'id', 'sub_recipe');
    }

}
