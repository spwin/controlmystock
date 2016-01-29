<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipes extends Model {

    protected $fillable = ['title'];

    public function items() {
        return $this->hasMany('App\Models\RecipeItems', 'recipe_id', 'id');
    }

}