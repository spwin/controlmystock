<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockPeriods extends Model {

	protected $fillable = ['date_from', 'date_to', 'number'];

}