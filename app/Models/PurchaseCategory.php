<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseCategory extends Model {

    protected $fillable = [
        'title'
    ];

    public function invoices(){
        return $this->hasMany('App\Models\Purchases', 'category_id', 'id');
    }

}
