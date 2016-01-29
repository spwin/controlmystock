<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model {

	protected $fillable = ['user_id', 'username', 'url', 'action', 'message', 'object_id'];

    public function user(){
        $this->hasOne('App\User', 'id', 'user_id');
    }
}
