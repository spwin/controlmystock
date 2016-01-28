<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitGroups extends Model
{
    protected $fillable = [
        'title',
        'disable_delete'
    ];

    public function units(){
        return $this->hasMany('App\Models\Units', 'group_id');
    }
}
