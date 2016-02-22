<?php namespace App\Http\Controllers;

use App\Models\Files;
use App\Models\Menu;
use App\Models\SaleItems;
use Helper;
use Illuminate\Support\Facades\Input;
use App\Models\StockPeriods;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SalesController extends Controller {

    private $title = 'Sale';
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
        foreach($periods as $period){
            $period_list[$period->id] = 'Stock #'.$period->number.' ('.$period->date_from.' - '.($period->id == $currentPeriodId ? 'NOW' : $period->date_to).')';
        }
        if(Input::has('stock_period')){
            $currentPeriodId = Input::get('stock_period');
        }
        $sales = Sales::where(['stock_period_id' => $currentPeriodId])->get();
		return view('Sales.index')->with(array(
            'title' => $this->title,
            'period' => $currentPeriodId,
            'items' => $sales,
            'stocks_list' => $period_list
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
        foreach($periods as $period){
            $period_list[$period->id] = 'Stock #'.$period->number.' ('.$period->date_from.' - '.($period->id == $currentPeriodId ? 'NOW' : $period->date_to).')';
        }
        if(Input::has('stock_period')){
            $currentPeriodId = Input::get('stock_period');
        }
        return view('Sales.create')->with(array(
            'title' => $this->title,
            'period' => $currentPeriodId,
            'stocks_list' => $period_list
        ));
	}

    function _import_csv($path, $filename)
    {
        $csv_config = [
            'start_from_line' => 11,
            'number_row' => 1,
            'title_row' => 2,
            'quantity_row' => 3,
            'price_row' => 4
        ];
        $csv = $path .'/'. $filename;
        $content = array_map('str_getcsv', file($csv));
        $data = [];
        for($i = $csv_config['start_from_line']; $i < count($content); $i++){
            if(is_numeric($content[$i][$csv_config['number_row']])) {
                $data[] = [
                    'number' => $content[$i][$csv_config['number_row']],
                    'title' => $content[$i][$csv_config['title_row']],
                    'quantity' => $content[$i][$csv_config['quantity_row']],
                    'price' => $content[$i][$csv_config['price_row']],
                ];
            }
        }
        return $data;
    }

    function postUpload (Request $request)
    {
        $file = $request->file('file');
        $input = $request->all();
        $validator = Validator::make(
            [
                'file'      => $file,
                'extension' => strtolower($file ? $file->getClientOriginalExtension() : 'sss'),
            ],
            [
                'file'          => 'required',
                'extension'      => 'required|in:csv',
            ]
        );
        if ($validator->fails()) {
            return Redirect::action('MenusController@index')->withErrors($validator);
        }
        $data = [];
        if (Input::hasFile('file')){
            if(array_key_exists('file', $input)){
                if($input['file']->isValid()){
                    $filename = 'aloha_'.date('Y_m_d_His').'.'.$input['file']->getClientOriginalExtension();
                    $path = storage_path().'/upload';
                    $input['file']->move($path, $filename);
                    $file = Files::create(['filename' => $filename]);
                    $data = $this->_import_csv($path, $file->filename);
                }
            }
        }
        return $data;
    }

    public function uploadMenu (Request $request)
    {
        $data = $this->postUpload($request);
        if(is_array($data)) {
            $before = Menu::count();
            $sale = Sales::create(['stock_period_id' => $request->get('stock_period_id')]);
            foreach ($data as $item) {
                $menu = Menu::updateOrCreate(
                    ['number' => $item['number']],
                    ['number' => $item['number'], 'title' => $item['title'], 'price' => $item['price']]
                );
                $data = [
                    'quantity' => $item['quantity'],
                    'menu_id' => $menu->id,
                    'sale_id' => $sale->id,
                    'price' => $item['price'],
                    'total_price' => round($item['price']*$item['quantity'], 2)
                ];
                SaleItems::create($data);
            }
            $difference = Menu::count() - $before;
            Helper::add('', 'uploaded '.$difference.' menu items import file on import sales action');
            Session::flash('flash_message', 'Sale uploaded successfully with '.$difference.' new items added to menu.');
            return Redirect::action('SalesController@index', ['stock_period' => $request->get('stock_period_id')]);
        } else {
            return $data;
        }
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
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
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id, Request $request)
	{
        $sale = Sales::findOrFail($id);
        $sale->delete();
        Helper::add($id, 'deleted sale');
        Session::flash('flash_message', $this->title.' item successfully deleted!');
        $variables = $request->exists('stock_period_id') ? ['stock_period' => $request->get('stock_period_id')] : [];
        return Redirect::action('SalesController@index', $variables);
	}

}
