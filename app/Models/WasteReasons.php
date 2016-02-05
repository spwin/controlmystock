<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteReasons extends Model {

	protected $fillable = ['reason'];

    public function wastes(){
        return $this->hasMany('App\Models\Wastes', 'reason_id', 'id');
    }

}
