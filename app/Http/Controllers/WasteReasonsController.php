<?php namespace App\Http\Controllers;

use Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\WasteReasons;
use Illuminate\Http\Request;

class WasteReasonsController extends Controller {

    private $title = 'Waste reason';
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $reasons = WasteReasons::all();
		return view('Reasons.index')->with(array(
            'title' => $this->title,
            'reasons' => $reasons
        ));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        return view('Reasons.create')->with(array(
            'title' => $this->title
        ));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
    public function store(Request $request){
        $this->validate($request, [
            'reason' => 'required|max:500'
        ]);
        $input = $request->all();

        WasteReasons::create($input);
        Helper::add(DB::getPdo()->lastInsertId(), 'added new waste reason (ID '.DB::getPdo()->lastInsertId().')');
        Session::flash('flash_message', $this->title.' successfully added!');

        return Redirect::action('WasteReasonsController@index');
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
        $reason = WasteReasons::findOrFail($id);
        return view('Reasons.edit')->with(array(
            'title' => $this->title,
            'reason' => $reason
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
        $reason = WasteReasons::findOrFail($id);

        $this->validate($request, [
            'reason' => 'required|max:500'
        ]);

        $input = $request->all();

        $reason->fill($input)->save();
        Helper::add($id, 'edited waste reason (ID '.$id.')');
        Session::flash('flash_message', $this->title.' successfully updated!');

        return Redirect::action('WasteReasonsController@index');
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        $reason = WasteReasons::findOrFail($id);

        $reason->delete();
        Helper::add($id, 'deleted waste reason (ID '.$id.')');
        Session::flash('flash_message', $this->title.' successfully deleted!');


        return Redirect::action('WasteReasonsController@index');
	}

}
