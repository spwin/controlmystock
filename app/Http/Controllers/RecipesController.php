<?php namespace App\Http\Controllers;

use Helper;
use App\Http\Requests;
use App\Models\Recipes;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class RecipesController extends Controller {

    private $title = 'Recipe';
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $search = Input::get('q');
        $recipes = $search ? Recipes::where('title', 'LIKE', '%'.$search.'%')->orderBy('created_at', 'DESC')->get() :  Recipes::orderBy('created_at', 'DESC')->get();
		return view('Recipes.index')->with(array(
            'title' => $this->title,
            'recipes' => $recipes,
            'search' => $search
        ));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
    public function create(){
        return view('Recipes.create')->with(array(
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
            'title' => 'required|unique:recipes|max:200'
        ]);
        $input = $request->all();

        $result = Recipes::create($input);
        Helper::add($result->id, 'added recipe '.$result->title.' (ID '.$result->id.')');

        Session::flash('flash_message', $this->title.' successfully added!');

        return Redirect::action('RecipeItemsController@index', $result->id);
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
        $Recipe = Recipes::findOrFail($id);
        return view('Recipes.edit')->with(array(
            'title' => $this->title,
            'Recipe' => $Recipe
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
        $Recipe = Recipes::findOrFail($id);

        $this->validate($request, [
            'title' => 'required|unique:recipes,id,'.$Recipe->id.'|max:200'
        ]);

        $input = $request->all();

        $Recipe->fill($input)->save();
        Helper::add($id, 'edited recipe '.$Recipe->title.' (ID '.$Recipe->id.')');

        Session::flash('flash_message', $this->title.' successfully updated!');

        return Redirect::action('RecipesController@index');
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
    public function destroy($id)
    {
        $Recipe = Recipes::findOrFail($id);

        Session::flash('flash_message', $this->title.' successfully deleted!');
        Helper::add($id, 'deleted recipe '.$Recipe->title.' (ID '.$Recipe->id.')');
        $Recipe->delete();

        return Redirect::action('RecipesController@index');
    }

}
