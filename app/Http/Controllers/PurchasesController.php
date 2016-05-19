<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Files;
use App\Models\PurchaseCategory;
use App\Models\Purchases;
use App\Models\StockPeriods;
use App\Models\Suppliers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Helper;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;

class PurchasesController extends Controller {

    private $title = 'Purchase';
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $currentPeriodId = Helper::defaultPeriodId();
        $periods = StockPeriods::all();
        $period_list = array();
        $period_dates = array();
        foreach($periods as $period){
            $period_list[$period->id] = 'Stock #'.$period->number.' ('.$period->date_from.' - '.($period->date_to ? $period->date_to : 'NOW').')';
            $period_dates[$period->id] = ['from' => date('Y-m-d', strtotime($period->date_from)), 'to' => date('Y-m-d', strtotime($period->date_to))];
        }
        $running = Helper::currentPeriodId();
        if(Input::has('stock_period')){
            $currentPeriodId = Input::get('stock_period');
        }
        $date_from = Input::has('date_from') ? Input::get('date_from') : $period_dates[$currentPeriodId]['from'];
        $date_to = Input::has('date_to') ? Input::get('date_to') : ($period_dates[$currentPeriodId]['to'] ? $period_dates[$currentPeriodId]['to'] : date('Y-m-d', time()));
		return view('Purchases.index')->with(array(
            'title' => $this->title,
            'items' => Purchases::where('vat_date', '>=', $date_from)->where('date_created', '<=', $date_to)->orderBy('vat_date', 'DESC')->get(),
            'period' => $currentPeriodId,
            'running_period' => $running,
            'stocks_list' => $period_list,
            'period_dates' => $period_dates,
            'date_from' => $date_from,
            'date_to' => $date_to
        ));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        $currentPeriodId = Helper::defaultPeriodId();
        $periods = StockPeriods::all();
        $period_list = array();
        $categories = PurchaseCategory::lists('title', 'id');
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
            'suppliers' => Suppliers::orderBy('title', 'ASC')->lists('title', 'id'),
            'categories' => $categories
        ));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
        $this->validate($request, [
            'number' => 'unique:purchases,number'
        ]);
        $input = $request->all();
        if(array_key_exists('new_supplier', $input)){
            $check_supplier = Suppliers::where(['title' => $input['custom_supplier']])->get();
            if(count($check_supplier) > 0 ){
                $this->validate($request, [
                    'custom_supplier' => 'unique:suppliers,title'
                ]);
                echo 'here';
            } else {
                $supplier = Suppliers::create(['title' => $input['custom_supplier']]);
                $input['supplier_id'] = $supplier->id;
                echo 'there';
            }
        }
        if($input['category_id'] == 0) $input['category_id'] = null;
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
        $currentPeriodId = Helper::defaultPeriodId();
        $item = Purchases::findOrFail($id);
        $periods = StockPeriods::all();
        $categories = PurchaseCategory::lists('title', 'id');
        $period_list = array();
        foreach($periods as $period){
            $period_list[$period->id] = 'Stock #'.$period->number.' ('.$period->date_from.' - '.($period->id == $currentPeriodId ? 'NOW' : $period->date_to).')';
        }
        return view('Purchases.edit')->with(array(
            'title' => $this->title,
            'item' => $item,
            'stocks_list' => $period_list,
            'suppliers' => Suppliers::orderBy('title', 'ASC')->lists('title', 'id'),
            'categories' => $categories
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
        if($input['category_id'] == 0) $input['category_id'] = null;
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
        return Redirect::action('ItemPurchasesController@index', $item->id);
	}

    function getData(){
        $currentPeriodId = Helper::defaultPeriodId();
        $periods = StockPeriods::all();
        $period_list = array();
        $period_dates = array();
        foreach($periods as $period){
            $period_list[$period->id] = 'Stock #'.$period->number.' ('.$period->date_from.' - '.($period->date_to ? $period->date_to : 'NOW').')';
            $period_dates[$period->id] = ['from' => date('Y-m-d', strtotime($period->date_from)), 'to' => date('Y-m-d', strtotime($period->date_to))];
        }
        if(Input::has('stock_period')){
            $currentPeriodId = Input::get('stock_period');
        }
        $date_from = Input::has('date_from') ? Input::get('date_from') : $period_dates[$currentPeriodId]['from'];
        $date_to = Input::has('date_to') ? Input::get('date_to') : ($period_dates[$currentPeriodId]['to'] ? $period_dates[$currentPeriodId]['to'] : date('Y-m-d', time()));
        $purchases_list = Purchases::where('date_created', '>=', $date_from)->where('date_created', '<=', $date_to)->orderBy('date_created', 'DESC')->get();
        $purchases = array();
        foreach($purchases_list as $purchase) {
            $NET = round($purchase->purchases()->sum('price'), 2);
            $VAT = round($purchase->purchases()->sum('vat'), 2);
            $purchases['items'][$purchase->id] = [
                'number' => $purchase->number,
                'date_created' => $purchase->date_created,
                'vat_date' => $purchase->vat_date,
                'supplier' => $purchase->supplier()->first() ? $purchase->supplier()->first()->title : '',
                'category' => $purchase->category()->first() ? $purchase->category()->first()->title : '',
                'NET' => $NET,
                'VAT' => $VAT,
                'GROSS' => $NET + $VAT,
                'status' => $purchase->status ? 'PAID' : 'PENDING'
            ];
        }
        $purchases['from'] = $date_from;
        $purchases['to'] = $date_to;
        return $purchases;
    }

    public function exportExcel() {
        $data = $this->getData();
        $ready = [];
        $total_net = 0;
        $total_vat = 0;
        foreach($data['items'] as $key => $d){
            $ready[$key] = [
                'ID' => $key,
                'Invoice number' => $d['number'],
                'Category' => $d['category'],
                'Date created' => $d['date_created'],
                'VAT date' => $d['vat_date'],
                'Supplier' => $d['supplier'],
                'NET' => $d['NET'],
                'VAT' => $d['VAT'],
                'GROSS' => $d['GROSS'],
                'Status' => $d['status']
            ];
            $total_net += $d['NET'];
            $total_vat += $d['VAT'];
        }
        $ready[] = [
            'ID' => '',
            'Invoice number' => '',
            'Category' => '',
            'Date created' => '',
            'VAT date' => '',
            'Supplier' => 'TOTAL',
            'NET' => $total_net,
            'VAT' => $total_vat,
            'GROSS' => ($total_net+$total_vat),
            'Status' => ''
        ];
        Excel::create('Purchases', function($excel) use($ready, $data)
        {
            $excel->sheet('Sheetname', function($sheet) use($ready, $data)
            {
                $sheet->setAutoSize(true);
                $sheet->mergeCells('A1:J1');
                $sheet->mergeCells('A2:J2');
                //header
                $sheet->setHeight(1, 40);
                $sheet->row(1, function ($row) {
                    $row->setFontSize(30);
                });
                $sheet->row(1, array('Purchases ('.$data['from'].' - '.$data['to'].')'));
                //category
                /*$sheet->setHeight(2, 30);
                $sheet->row(2, function ($row) {
                    $row->setFontSize(20);
                });
                $sheet->row(2, array('Category: '.$c->title));*/
                //table headers
                $sheet->setHeight(3, 20);
                $sheet->row(3, function ($row) {
                    $row->setFontWeight('bold');
                });
                $sheet->cells('C', function($cells) {
                    $cells->setAlignment('left');
                    $cells->setFontSize(10);
                });
                //table data
                $sheet->setFontSize(10);
                $sheet->fromArray($ready, null, 'A3');
            });
        })->export('xls');
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

    public function checkNumber(){
        $number = Input::get('number');
        $invoice = Purchases::where(['number' => $number])->first();
        if(count($invoice) == 0)
            return json_encode('<span class="text-success">All fine. You can use this invoice number.</span>');
        else
            return json_encode('<span class="text-danger">This invoice number is in use. <a href="'.URL::action('ItemPurchasesController@index', $invoice->id).'">See invoice No.'.$number.'</a></span>');
    }

}
