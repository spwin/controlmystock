<?php namespace App\Http\Controllers;

use App\Models\Items;
use App\Models\ItemUnits;
use App\Models\Menu;
use App\Models\Recipes;
use App\Models\WasteReasons;
use App\Models\Wastes;
use Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Models\StockPeriods;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class WastesController extends Controller {

    private $title = 'Waste';
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $currentPeriodId = Helper::currentPeriodId();
        $periods = StockPeriods::all();
        $period_list = array();
        foreach($periods as $period){
            $period_list[$period->id] = 'Stock #'.$period->number.' ('.$period->date_from.' - '.($period->id == $currentPeriodId ? 'NOW' : $period->date_to).')';
        }
        if(Input::has('stock_period')){
            $currentPeriodId = Input::get('stock_period');
        }
        $items = Wastes::where(['stock_period_id' => $currentPeriodId])->get();
        return view('Wastes.index')->with(array(
            'title' => $this->title,
            'period' => $currentPeriodId,
            'stocks_list' => $period_list,
            'items' => $items
        ));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        $currentPeriodId = Helper::currentPeriodId();
        $periods = StockPeriods::all();
        $period_list = array();
        foreach($periods as $period){
            $period_list[$period->id] = 'Stock #'.$period->number.' ('.$period->date_from.' - '.($period->id == $currentPeriodId ? 'NOW' : $period->date_to).')';
        }
        if(Input::has('stock_period')){
            $currentPeriodId = Input::get('stock_period');
        }
        $select_recipes = Recipes::orderBy('title', 'ASC')->lists('title', 'id');
        $items = ItemUnits::orderBy('default', 'DESC')->get();
        $items_units = [];
        foreach($items as $item){
            $items_units['list'][$item->item()->first()->id][] = ['id' => $item->id, 'title' => $item->unit()->first()->title];
            $items_units['php_list'][$item->item()->first()->id][$item->id] = $item->unit()->first()->title;
            $items_units['factors'][$item->id] = $item->factor;
        }
        $select_items = Items::orderBy('title', 'ASC')->lists('title', 'id');
        $select_menus = Menu::orderBy('title', 'ASC')->lists('title', 'id');
        $select_reasons = WasteReasons::orderBy('reason', 'ASC')->lists('reason', 'id');
        return view('Wastes.create')->with(array(
            'title' => $this->title,
            'recipes' => $select_recipes,
            'items' => $select_items,
            'items_units' => $items_units,
            'menus' => $select_menus,
            'period' => $currentPeriodId,
            'stocks_list' => $period_list,
            'reasons' => $select_reasons
        ));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
    public function store(Request $request)
    {
        $input = $request->all();
        Wastes::create($input);
        Helper::add(DB::getPdo()->lastInsertId(), 'added waste (ID: '.DB::getPdo()->lastInsertId().')');
        Session::flash('flash_message', $this->title.' successfully added!');
        return Redirect::action('WastesController@index');
    }

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        $waste = Wastes::findOrFail($id);
        $currentPeriodId = Helper::currentPeriodId();
        $periods = StockPeriods::all();
        $period_list = array();
        foreach($periods as $period){
            $period_list[$period->id] = 'Stock #'.$period->number.' ('.$period->date_from.' - '.($period->id == $currentPeriodId ? 'NOW' : $period->date_to).')';
        }
        if(Input::has('stock_period')){
            $currentPeriodId = Input::get('stock_period');
        }
        $select_recipes = Recipes::orderBy('title', 'ASC')->lists('title', 'id');
        $items = ItemUnits::orderBy('default', 'DESC')->get();
        $items_units = [];
        foreach($items as $item){
            $items_units['list'][$item->item()->first()->id][] = ['id' => $item->id, 'title' => $item->unit()->first()->title];
            $items_units['php_list'][$item->item()->first()->id][$item->id] = $item->unit()->first()->title;
            $items_units['factors'][$item->id] = $item->factor;
        }
        $select_items = Items::orderBy('title', 'ASC')->lists('title', 'id');
        $select_menus = Menu::orderBy('title', 'ASC')->lists('title', 'id');
        $select_reasons = WasteReasons::orderBy('reason', 'ASC')->lists('reason', 'id');
        return view('Wastes.edit')->with(array(
            'title' => $this->title,
            'recipes' => $select_recipes,
            'items' => $select_items,
            'items_units' => $items_units,
            'menus' => $select_menus,
            'period' => $currentPeriodId,
            'stocks_list' => $period_list,
            'reasons' => $select_reasons,
            'waste' => $waste
        ));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, Request $request)
	{
        $waste = Wastes::findOrFail($id);
        $input = $request->all();
        $waste->update($input);
        $waste->save();
        Helper::add($waste->id, 'edited waste (ID: '.$waste->id.')');
        Session::flash('flash_message', $this->title.' successfully edited!');
        return Redirect::action('WastesController@index');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$waste = Wastes::findOrFail($id);
        $waste->delete();
        Helper::add(DB::getPdo()->lastInsertId(), 'deleted waste (ID: '.DB::getPdo()->lastInsertId().')');
        Session::flash('flash_message', $this->title.' successfully deleted!');
        return Redirect::action('WastesController@index');
	}

}
