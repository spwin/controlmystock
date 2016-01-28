<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Items;
use App\Models\ItemUnits;
use App\Models\StockCheck;
use App\Models\Units;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class ItemUnitsController extends Controller {

    private $title = 'Item Units';

    public function index($itemId){
        return view('ItemUnits.index')->with(array(
            'title' => $this->title,
            'item' => Items::findOrFail($itemId),
            'items' => ItemUnits::where('item_id', $itemId)->get(),
            'default_unit' => ItemUnits::where(['item_id' => $itemId, 'default' => 1])->first(),
        ));
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
        if(array_key_exists('default', $input)){
            DB::table('item_units')->where(['default' => 1, 'item_id' => $input['item_id']])->update(['default' => 0]);
            $input['default'] = 1;
            DB::statement( 'INSERT INTO item_units (`item_id`, `unit_id`, `factor`, `default`, `updated_at`, `created_at`) VALUES ('.$input['item_id'].', '.$input['unit_id'].', '.$input['factor'].', '.$input['default'].', NOW(), NOW())' );
            ItemUnits::where(['item_id' => $input['item_id']])->update(['factor' => DB::raw('factor/'.$input['factor'])]);
            Items::where(['id' => $input['item_id']])->update(['stock' => DB::raw('stock/'.$input['factor'])]);
            StockCheck::where(['item_id' => $input['item_id']])->update(['before' => DB::raw('`before` / '.$input['factor']), 'after' => DB::raw('`after` / '.$input['factor'])]);
        } else {
            $input['default'] = 0;
            DB::statement( 'INSERT INTO item_units (`item_id`, `unit_id`, `factor`, `default`, `updated_at`, `created_at`) VALUES ('.$input['item_id'].', '.$input['unit_id'].', '.$input['factor'].', '.$input['default'].', NOW(), NOW())' );
        }
        //Units::create($input);
        Session::flash('flash_message', $this->title.' successfully added!');

        return Redirect::action('ItemsController@index');
    }

    public function edit($id)
    {

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
            Items::where(['id' => $input['item_id']])->update(['stock' => DB::raw('stock/'.$input['factor'])]);
            StockCheck::where(['item_id' => $input['item_id']])->update(['before' => DB::raw('`before` / '.$input['factor']), 'after' => DB::raw('`after` / '.$input['factor'])]);
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
        Items::where(['id' => $itemUnit->item_id])->update(['stock' => DB::raw('stock/'.$itemUnit->factor)]);
        StockCheck::where(['item_id' => $itemUnit->item_id])->update(['before' => DB::raw('`before` / '.$itemUnit->factor), 'after' => DB::raw('`after` / '.$itemUnit->factor)]);
        return Redirect::action('ItemUnitsController@index', array('item_id' => $itemUnit->item_id));
    }

}
