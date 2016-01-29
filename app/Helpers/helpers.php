<?php

use Illuminate\Support\Str;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\History;

class Helper {

    public static function add($object_id, $message) {
        $user = User::findOrFail(Auth::user()->id);
        History::create([
            'user_id' => $user->id,
            'username' => $user->name,
            'url' => Route::getCurrentRoute()->getName(),
            'action' => Route::getCurrentRoute()->getActionName(),
            'message' => $message,
            'object_id' => $object_id
        ]);
    }

    public static function currentPeriodId() {
        $period = \App\Models\StockPeriods::whereNull('date_to')->get();
        return count($period) > 0 ? $period->first()->id : 0;
    }

}