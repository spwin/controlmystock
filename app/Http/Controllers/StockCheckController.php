<?php namespace App\Http\Controllers;

use App\Models\StockItem;
use App\Models\StockPeriods;
use Helper;
use App\Http\Requests;
use App\Models\Items;
use App\Models\ItemUnits;
use App\Models\StockCheck;
use App\Models\Units;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class StockCheckController extends Controller {

    private $title = 'Stock Check';

    public function index(){
        $currentPeriodId = Helper::currentPeriodId();
        if($currentPeriodId) {
            $search = Input::get('q');
            $items = $search ? Items::where('title', 'LIKE', '%' . $search . '%')->get() : Items::all();
            if (count($items) == 1 && $search) {
                return redirect()->action('StockCheckController@edit', ['item_id' => $items->first()->id]);
            }
            $last_period = StockPeriods::where(['number' => StockPeriods::findOrFail($currentPeriodId)->number - 1])->get();
            return view('StockCheck.index')->with(array(
                'title' => $this->title,
                'items' => $search ? Items::where('title', 'LIKE', '%' . $search . '%')->take(10)->get() : Items::orderBy("updated_at", 'DESC')->take(10)->get(),
                'item_list' => Items::lists('title'),
                'search' => $search,
                'period' => $currentPeriodId,
                'previous' => count($last_period) > 0 ? $last_period->first()->id : 0
            ));
        } else {
            Session::flash('flash_message', 'You need to start a period first!');
            return Redirect::action('StockPeriodsController@index');
        }
    }

    public function autocomplete(){
        $term = Input::get('term');
        $results = array();
        $queries = DB::table('items')
            ->where('title', 'LIKE', '%'.$term.'%')
            ->take(5)->get();
        foreach ($queries as $query)
        {
            $results[] = [ 'id' => $query->id, 'value' => $query->title ];
        }
        return Response::json($results);
    }

    public function history($item){
        return 'history for item ID:'.$item;
    }

    public function create($itemId){
        $item = Items::findOrFail($itemId);
        return view('ItemUnits.create')->with(array(
            'title' => $this->title,
            'item' => $item,
            'units' => Units::whereNotIn('id', ItemUnits::where('item_id', $itemId)->lists('unit_id'))->lists('title', 'id'),
            'default_unit' => ItemUnits::where(['item_id' => $itemId, 'default' => 1])->first(),
            'unit_groups_list' => Units::lists('group_id', 'id'),
            'unit_factors_list' => Units::lists('factor', 'id')
        ));
    }

    public function store(Request $request){
        $input = $request->all();
        if($input['value'] == 0){
            Session::flash('flash_message', 'Quantity must be different to 0');
            return Redirect::action('StockCheckController@edit', ['item_id' => $input['item_id']])->withInput();
        }
        $item = Items::findOrFail($input['item_id']);
        $unit = ItemUnits::findOrFail($input['unit_id']);
        $value = $input['value'];
        $default_unit = $item->units()->where(['default' => 1])->first();
        if($default_unit->id != $input['unit_id']){
            $value = $unit->factor * $value;
        }
        $currentPeriodId = array_key_exists('stock_period_id', $input) ? $input['stock_period_id'] : Helper::currentPeriodId();
        $stockItem = StockItem::where(['stock_period_id' => $currentPeriodId, 'item_id' => $item->id])->get();
        if(count($stockItem) == 0){
            $stockItem = StockItem::create(['stock_period_id' => $currentPeriodId, 'item_id' => $item->id, 'stock' => 0]);
        } else {
            $stockItem = $stockItem->first();
        }
        switch ($input['action']) {
            case 'add' :
                $new_stock = $stockItem->stock + $value;
                break;
            case 'reduce' :
                $new_stock = $stockItem->stock - $value;
                break;
            case 'change' :
                $new_stock = $value;
                break;
            default:
                $new_stock = $item->stock + $value;
                break;
        }
        $data = [
            'item_id' => $item->id,
            'unit_id' => $unit->unit()->first()->id,
            'value' => $input['value'],
            'action' => $input['action'],
            'before' => $stockItem->stock,
            'after' => $new_stock,
            'stock_item_id' => $stockItem->id
        ];
        $stockItem->update(['stock' => $new_stock]);
        $stockItem->save();
        $item->touch();
        StockCheck::create($data);
        Helper::add(DB::getPdo()->lastInsertId(), $input['action'].'ed stock for '.$item->title.' (ID '.$item->id.')'.' with value '.$data['value'].' '.$unit->unit()->first()->title.($default_unit->id != $input['unit_id'] ? ' ('.$value.' '.$default_unit->unit()->first()->title.')' : ''));
        return Redirect::action('StockCheckController@edit', ['item_id' => $item->id, 'stock_period' => $currentPeriodId]);
    }

    public function modificate(){
        $items = Items::all();
        foreach($items as $item){
            $data = [
                'item_id' => $item->id,
                'stock_period_id' => Helper::currentPeriodId(),
                'stock' => $item->stock
            ];
            StockItem::create($data);
        }
        return Redirect::action('StockCheckController@index');
    }

    public function edit($id)
    {
        $item = Items::findOrFail($id);
        $other = $item->units()->where(['default' => 0])->first();
        $actions = [
            'add' => 'Add',
            'reduce' => 'Reduce by',
            'change' => 'Change to'
        ];
        $other_units = [];
        if($other){
            foreach($item->units()->where(['default' => 0])->get() as $unit){
                $other_units[$unit->id] = $unit->unit()->first()->title;
            }
        }
        $currentPeriodId = Helper::currentPeriodId();
        $periods = StockPeriods::all();
        $period_list = array();
        foreach($periods as $period){
            $period_list[$period->id] = 'Stock #'.$period->number.' ('.$period->date_from.' - '.($period->id == $currentPeriodId ? 'NOW' : $period->date_to).')';
        }
        if(Input::has('stock_period')){
            $currentPeriodId = Input::get('stock_period');
        }
        return view('StockCheck.edit')->with(array(
            'title' => $this->title,
            'item' => $item,
            'default' => $item->units()->where(['default' => 1])->first(),
            'other' => $other,
            'actions' => $actions,
            'other_units' => $other_units,
            'period' => $currentPeriodId,
            'stock' => StockCheck::join('stock_items', 'stock_checks.stock_item_id', '=', 'stock_items.id')->where(['stock_checks.item_id' => $id, 'stock_items.stock_period_id' => $currentPeriodId])->orderBy('stock_checks.created_at', 'DESC')->get(),
            'stocks_list' => $period_list
        ));
    }

    public function update($id, Request $request)
    {
        $Units = Units::findOrFail($id);
        $this->validate($request, [
            'title' => 'required|unique:units|max:100'
        ]);
        $input = $request->all();
        if(array_key_exists('default', $input)){
            ItemUnits::where(['default' => 1, 'item_id' => $input['item_id']])->where('id', '!=', $id)->update(['default' => 0]);
            ItemUnits::where(['item_id' => $input['item_id']])->update(['factor' => DB::raw('factor/'.$input['factor'])]);
            $input['default'] = 1;
        }
        $Units->fill($input)->save();
        Session::flash('flash_message', $this->title.' successfully added!');

        return Redirect::action('UnitsController@index');
    }

    public function destroy($id)
    {
        $ItemUnits = ItemUnits::findOrFail($id);
        if($ItemUnits->default){
            $first = ItemUnits::where('id', '!=', $id)->where(['item_id' => $ItemUnits->item_id])->first();
            if($first){
                $first->update(['default' => 1]);
                ItemUnits::where(['item_id' => $ItemUnits->item_id])->update(['factor' => DB::raw('factor/'.$first->factor)]);
            }
        }
        $ItemUnits->delete();

        Session::flash('flash_message', $this->title.' successfully deleted!');

        return Redirect::action('ItemUnitsController@index', ['item_id' => $ItemUnits->item_id]);
    }

    public function setDefault($id)
    {
        $itemUnit = ItemUnits::findOrFail($id);
        ItemUnits::where('item_id', $itemUnit->item_id)->update(['default' => 0]);
        ItemUnits::where('id', $id)->update(['default' => 1]);
        ItemUnits::where(['item_id' => $itemUnit->item_id])->update(['factor' => DB::raw('factor/'.$itemUnit->factor)]);
        return Redirect::action('ItemUnitsController@index', array('item_id' => $itemUnit->item_id));
    }

}
