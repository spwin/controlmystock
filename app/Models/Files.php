<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Files extends Model {

	protected $fillable = ['filename'];

    public function purchases(){
        return $this->hasMany('App\Models\Purchases', 'invoice_id', 'id');
    }

}
