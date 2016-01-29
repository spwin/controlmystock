<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockCheck extends Model {

    protected $fillable = [
        'item_id',
        'unit_id',
        'value',
        'after',
        'before',
        'action',
        'stock_period_id'
    ];

    public function item() {
        return $this->hasOne('App\Models\Items', 'id', 'item_id');
    }

    public function unit() {
        return $this->hasone('App\Models\Units', 'id', 'unit_id');
    }

    public function period() {
        return $this->hasOne('App\Models\StockPeriods', 'id', 'stock_period_id');
    }

}
