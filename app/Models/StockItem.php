<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockItem extends Model {

	protected $fillable = ['item_id', 'stock_id', 'stock'];

}
