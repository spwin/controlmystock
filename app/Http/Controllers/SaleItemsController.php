<?php namespace App\Http\Controllers;

use Helper;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\SaleItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class SaleItemsController extends Controller {

    private $title = 'Sale item';
	/**
	 * Display a listing of the resource.
	 * @param  int  $id
	 * @return Response
	 */
	public function index($id)
	{
        $currentPeriodId = Helper::currentPeriodId();
        if(Input::has('stock_period')){
            $currentPeriodId = Input::get('stock_period');
        }
		return view('SaleItems.index')->with(array(
            'title' => $this->title,
            'items' => SaleItems::where(['sale_id' => $id])->get(),
            'period' => $currentPeriodId
        ));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
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
	public function destroy($id)
	{
		//
	}

}
