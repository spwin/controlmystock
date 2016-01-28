<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemUnits extends Model {

    protected $fillable = [
        'item_id',
        'unit_id',
        'default',
        'factor'
    ];

    public function item() {
        return $this->hasOne('App\Models\Items', 'id', 'item_id');
    }

    public function unit() {
        return $this->hasone('App\Models\Units', 'id', 'unit_id');
    }

}
