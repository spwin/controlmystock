<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suppliers extends Model {

	protected $fillable = ['vat', 'title', 'email', 'phone', 'address'];

    public function purchases(){
        return $this->hasMany('App\Models\Purchases', 'invoice_id', 'id');
    }
}
