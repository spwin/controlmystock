<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchases extends Model {

	protected $fillable = ['date_created', 'date_delivered', 'number', 'supplier_id', 'invoice_id', 'status', 'date_paid', 'stock_period_id', 'category_id'];

    public function supplier(){
        return $this->hasOne('App\Models\Suppliers', 'id', 'supplier_id');
    }

    public function invoice(){
        return $this->hasOne('App\Models\Files', 'id', 'invoice_id');
    }

    public function period(){
        return $this->hasOne('App\Models\StockPeriods', 'id', 'stock_period_id');
    }

    public function purchases(){
        return $this->hasMany('App\Models\ItemPurchases', 'purchase_id', 'id');
    }

    public function category() {
        return $this->belongsTo('App\Models\PurchaseCategory');
    }
}
