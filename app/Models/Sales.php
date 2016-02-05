<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model {

	protected $fillable = ['stock_period_id'];

    public function period(){
        return $this->hasOne('App\Models\StockPeriods', 'id', 'stock_period_id');
    }

    public function sales(){
        return $this->hasMany('App\Models\SaleItems', 'sale_id', 'id');
    }

}
