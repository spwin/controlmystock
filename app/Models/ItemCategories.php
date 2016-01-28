<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemCategories extends Model {

    protected $fillable = [
        'title',
        'parent_id'
    ];

    public function items(){
        return $this->hasMany('App\Models\Items', 'category_id', 'id');
    }

    public function children(){
        return $this->hasMany('App\Models\ItemCategories', 'parent_id');
    }

    public function parent(){
        return $this->belongsTo('App\Models\ItemCategories', 'parent_id', 'id');
    }

}
