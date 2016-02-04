<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Suppliers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Helper;

class SuppliersController extends Controller {

    private $title = 'Supplier';
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('Suppliers.index')->with(array(
            'title' => $this->title,
            'items' => Suppliers::all()
        ));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        return view('Suppliers.create')->with(array(
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
            'vat' => 'max:20'
        ]);
        $input = $request->all();
        Suppliers::create($input);
        Helper::add(DB::getPdo()->lastInsertId(), 'added new suppliers '.$input['title'].' (ID '.DB::getPdo()->lastInsertId().')');
        Session::flash('flash_message', $this->title.' successfully added!');

        return Redirect::action('SuppliersController@index');
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
        $Suppliers = Suppliers::findOrFail($id);
        return view('Suppliers.edit')->with(array(
            'title' => $this->title,
            'Suppliers' => $Suppliers
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
        $Suppliers = Suppliers::findOrFail($id);

        $this->validate($request, [
            'vat' => 'max:20'
        ]);

        $input = $request->all();

        $Suppliers->fill($input)->save();
        Helper::add($id, 'edited supplier '.$Suppliers->title.' (ID '.$id.')');
        Session::flash('flash_message', $this->title.' successfully updated!');

        return Redirect::action('SuppliersController@index');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        $Suppliers = Suppliers::findOrFail($id);
        $Suppliers->delete();
        Helper::add($id, 'deleted supplier '.$Suppliers->title.' (ID '.$id.')');
        Session::flash('flash_message', $this->title.' successfully deleted!');

        return Redirect::action('SuppliersController@index');
	}

}
