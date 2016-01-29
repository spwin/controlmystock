<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\History;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class HistoryController extends Controller {

    private $title = 'History';

    public function index(){
        $search = Input::get('q');
        $items = $search ? History::where('message', 'LIKE', '%'.$search.'%')->orWhere('username', 'LIKE', '%'.$search.'%')->orderBy('created_at', 'DESC')->get() :  History::orderBy('created_at', 'DESC')->get();
        return view('History.index')->with([
            'title' => $this->title,
            'items' => $items,
            'search' => $search
        ]);
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
