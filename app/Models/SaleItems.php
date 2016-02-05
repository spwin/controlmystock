<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItems extends Model {

	protected $fillable = ['quantity', 'sale_id', 'menu_id', 'price', 'total_price'];

    public function sale(){
        return $this->hasOne('App\Models\Sales', 'id', 'sale_id');
    }

    public function menu(){
        return $this->hasOne('App\Models\Menu', 'id', 'menu_id');
    }
}
