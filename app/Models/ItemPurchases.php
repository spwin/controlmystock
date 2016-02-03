<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPurchases extends Model {

	protected $fillable = ['purchase_id', 'type', 'item_id', 'unit_id', 'item_custom', 'unit_custom', 'value', 'price', 'vat', 'value_entered'];

    public function purchase(){
        return $this->hasOne('App\Models\Purchases', 'id', 'purchase_id');
    }

    public function item(){
        return $this->hasOne('App\Models\Items', 'id', 'item_id');
    }

    public function unit(){
        return $this->hasOne('App\Models\Units', 'id', 'unit_id');
    }
}
