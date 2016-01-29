<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\User;
use Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UsersController extends Controller {

    private $title = 'Users';

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        return view('Users.index')->with([
            'title' => $this->title,
            'users' => User::all()
        ]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
    public function create(){
        return view('Users.create')->with(array(
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
            'name' => 'required|unique:users|max:100'
        ]);
        $input = $request->all();
        $active = array_key_exists('active', $input) ? 1 : 0;
        $input['active'] = $active;
        $input['password'] = Hash::make($input['password']);
        User::create($input);
        Helper::add(DB::getPdo()->lastInsertId(), 'added user '.$input['name'].' (ID '.DB::getPdo()->lastInsertId().')');
        Session::flash('flash_message', $this->title.' successfully added!');

        return Redirect::action('UsersController@index');
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
        $User = User::findOrFail($id);
        return view('Users.edit')->with(array(
            'title' => $this->title,
            'User' => $User
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
        $User = User::findOrFail($id);
        $input = $request->all();
        if(array_key_exists('password', $input)){
            $input['password'] = Hash::make($input['password']);
        }
        $active = array_key_exists('active', $input) ? 1 : 0;
        $input['active'] = $active;
        $User->fill($input)->save();
        Helper::add($User->id, 'edited user '.$User->name.' (ID '.$User->id.')');
        Session::flash('flash_message', $this->title.' successfully updated!');
        return Redirect::action('UsersController@index');
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
    public function destroy($id)
    {
        $User = User::findOrFail($id);

        Helper::add($User->id, 'deleted user '.$User->name.' (ID '.$User->id.')');
        $User->delete();

        Session::flash('flash_message', $this->title.' successfully deleted!');

        return Redirect::action('UsersController@index');
    }

}
