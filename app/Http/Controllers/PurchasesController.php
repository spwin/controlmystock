<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Files;
use App\Models\Purchases;
use App\Models\StockPeriods;
use App\Models\Suppliers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Helper;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class PurchasesController extends Controller {

    private $title = 'Purchase';
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('Purchases.index')->with(array(
            'title' => $this->title,
            'items' => Purchases::orderBy('date_delivered', 'DESC')->get()
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
		return view('Purchases.create')->with(array(
            'title' => $this->title,
            'stocks_list' => $period_list,
            'period' => $currentPeriodId,
            'suppliers' => Suppliers::lists('title', 'id')
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
        if(array_key_exists('status', $input)){
            $input['status'] = 1;
            $input['date_paid'] = date('Y-m-d H:i:s', time());
        } else {
            $input['status'] = 0;
        }
        if(array_key_exists('file', $input)){
            if($input['file']->isValid()){
                $filename = 'invoice_'.date('Y_m_d_His').'.'.$input['file']->getClientOriginalExtension();
                $input['file']->move(storage_path().'/upload', $filename);
                $file = Files::create(['filename' => $filename]);
                $input['invoice_id'] = $file->id;
            }
        }
        $currentPeriodId = array_key_exists('stock_period_id', $input) ? $input['stock_period_id'] : Helper::currentPeriodId();
        $input['stock_period_id'] = $currentPeriodId;
        $purchase = Purchases::create($input);
        Helper::add(DB::getPdo()->lastInsertId(), 'created new invoice ID '.DB::getPdo()->lastInsertId());
        return Redirect::action('ItemPurchasesController@index', $purchase->id);
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
        $currentPeriodId = Helper::currentPeriodId();
        $item = Purchases::findOrFail($id);
        $periods = StockPeriods::all();
        $period_list = array();
        foreach($periods as $period){
            $period_list[$period->id] = 'Stock #'.$period->number.' ('.$period->date_from.' - '.($period->id == $currentPeriodId ? 'NOW' : $period->date_to).')';
        }
        return view('Purchases.edit')->with(array(
            'title' => $this->title,
            'item' => $item,
            'stocks_list' => $period_list,
            'suppliers' => Suppliers::lists('title', 'id')
        ));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  Request  $request
	 * @return Redirect
	 */
	public function update($id, Request $request)
	{
        $item = Purchases::findOrFail($id);
        $input = $request->all();
        if(array_key_exists('status', $input)){
            $input['status'] = 1;
            $input['date_paid'] = date('Y-m-d H:i:s', time());
        } else {
            $input['status'] = 0;
        }
        if(array_key_exists('file', $input)){
            if($input['file']->isValid()){
                $filename = 'invoice_'.date('Y_m_d_His').'.'.$input['file']->getClientOriginalExtension();
                $input['file']->move(storage_path().'/upload', $filename);
                $file = Files::create(['filename' => $filename]);
                $input['invoice_id'] = $file->id;
            }
        }
        $currentPeriodId = array_key_exists('stock_period_id', $input) ? $input['stock_period_id'] : Helper::currentPeriodId();
        $input['stock_period_id'] = $currentPeriodId;
        $item->update($input);
        $item->save();
        Helper::add(DB::getPdo()->lastInsertId(), 'updated invoice ID '.DB::getPdo()->lastInsertId());
        return Redirect::action('PurchasesController@index');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$purchase = Purchases::findOrFail($id);
        $purchase->delete();
        Helper::add($id, 'deleted purchase');
        Session::flash('flash_message', $this->title.' successfully deleted!');
        return Redirect::action('PurchasesController@index');
	}

}
