<?php namespace App\Http\Controllers;

use Helper;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\StockPeriods;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class StockPeriodsController extends Controller {

    private $title = 'Stock period';
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $periods = StockPeriods::orderBy('date_from', 'DESC')->get();
		return view('StockPeriods.index')->with(array(
            'title' => $this->title,
            'periods' => $periods
        ));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('StockPeriods.create')->with(array(
            'title' => $this->title
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
        $periods = StockPeriods::orderBy('date_from', 'DESC')->get();
        $input['number'] = 1;
        if(count($periods) > 0){
            $input['number'] = $periods->first()->number + 1;
        }
        if(array_key_exists('last_period', $input)){
            $last_period = StockPeriods::findOrFail($input['last_period']);
            $last_period->update(['date_to' => $input['date_from']]);
            $last_period->save();
        }
        StockPeriods::create($input);
        Helper::add(DB::getPdo()->lastInsertId(), '');
        return Redirect::action('StockPeriodsController@index');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function close($id)
	{
		$period = StockPeriods::findOrFail($id);
        return view('StockPeriods.close')->with(array(
            'title' => $this->title,
            'number' => $period->number + 1,
            'from_min' => $period->date_from,
            'last_period' => $id
        ));
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
        $period = StockPeriods::findOrFail($id);
        $period->delete();

        Session::flash('flash_message', $this->title.' successfully deleted!');

        return Redirect::action('StockPeriodsController@index');
	}

}
