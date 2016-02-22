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

    public static function lastPeriodId() {
        $current = \App\Models\StockPeriods::whereNull('date_to')->get();
        if(count($current) > 0){
            $number = $current->first()->number - 1;
            $period = \App\Models\StockPeriods::where(['number' => $number])->get();
        }
        return count($period) > 0 ? $period->first()->id : 0;
    }

    public static function periodBeforeId($id) {
        $current = \App\Models\StockPeriods::find($id);
        if(count($current) > 0){
            $number = $current->number - 1;
            $period = \App\Models\StockPeriods::where(['number' => $number])->get();
        }
        return count($period) > 0 ? $period->first()->id : 0;
    }

    public static function periodAfterId($id) {
        $current = \App\Models\StockPeriods::find($id);
        if(count($current) > 0){
            $number = $current->number + 1;
            $period = \App\Models\StockPeriods::where(['number' => $number])->get();
        }
        return count($period) > 0 ? $period->first()->id : $id;
    }

    public static function defaultPeriodId() {
        if(\Illuminate\Support\Facades\Session::has('period')){
            $period = \Illuminate\Support\Facades\Session::get('period');
        } else {
            $period = Helper::lastPeriodId();
        }
        return $period;
    }

    public static function setDefaultPeriodId($id) {
        $period = \Illuminate\Support\Facades\Session::set('period', $id);
        return count($period) > 0 ? $period->first()->id : 0;
    }

    public static function getDefaultPeriod() {
        if(\Illuminate\Support\Facades\Session::has('period')){
            $p = \App\Models\StockPeriods::where(['id' => \Illuminate\Support\Facades\Session::get('period')])->get();
            $period = count($p) > 0 ? $p->first() : 0;
        } else {
            $current = \App\Models\StockPeriods::whereNull('date_to')->get();
            if(count($current) > 0){
                $number = $current->first()->number - 1;
                $p = \App\Models\StockPeriods::where(['number' => $number])->get();
            }
            $period = count($p) > 0 ? $p->first() : 0;
        }
        return $period;
    }
}