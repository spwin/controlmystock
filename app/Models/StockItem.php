<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockItem extends Model {

	protected $fillable = ['item_id', 'stock_period_id', 'stock'];

	public function item(){
		return $this->hasOne('App\Models\Items', 'id', 'item_id');
	}

	public function period(){
		return $this->hasOne('App\Models\StockPeriods', 'id', 'stock_period_id');
	}

	public function stockChecks(){
		return $this->hasMany('App\Models\StockChecks', 'stock_item_id', 'id');
	}
}
