<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Units extends Model
{
    protected $fillable = [
        'title',
        'factor',
        'default',
        'group_id',
        'disable_delete'
    ];

    public function group() {
        return $this->belongsTo('App\Models\UnitGroups');
    }

    public function itemUnits() {
        return $this->hasMany('App\Models\ItemUnits', 'unit_id', 'id');
    }
}
