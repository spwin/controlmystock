<?php namespace App\Http\Controllers;

use Helper;
use App\Http\Requests;
use App\Models\ItemUnits;
use App\Models\Recipes;
use App\Models\RecipeItems;
use App\Models\Items;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class RecipeItemsController extends Controller {

    private $title = 'Recipe Item';
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
    public function index($recipe){
        return view('RecipeItems.index')->with(array(
            'title' => $this->title,
            'recipe' => Recipes::findOrFail($recipe),
            'items' => RecipeItems::where('recipe_id', $recipe)->get()
        ));
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($recipe_id, $type)
	{
        $recipe = Recipes::findOrFail($recipe_id);
        if($type == 'recipe') {
            $included_recipes = RecipeItems::where(['recipe_id' => $recipe_id, 'type' => $type])->lists('sub_recipe');
            $included_recipes[] = $recipe_id;
            $select_recipes = $included_recipes ? Recipes::whereNotIn('id', $included_recipes )->lists('title', 'id') : Recipes::lists('title', 'id');
            if($select_recipes) {
                return view('RecipeItems.create_recipe')->with(array(
                    'title' => $this->title,
                    'recipe' => $recipe,
                    'recipes' => $select_recipes,
                    'type' => $type
                ));
            } else {
                Session::flash('flash_message', 'It looks like you have included all possible recipes already.');
                return Redirect::action('RecipeItemsController@index', $recipe->id);
            }
        } else {
            $items = ItemUnits::orderBy('default', 'DESC')->get();
            $items_units = [];
            foreach($items as $item){
                $items_units['list'][$item->item()->first()->id][] = ['id' => $item->id, 'title' => $item->unit()->first()->title];
                $items_units['php_list'][$item->item()->first()->id][$item->id] = $item->unit()->first()->title;
                $items_units['factors'][$item->id] = $item->factor;
            }
            $select_items = Items::whereNotIn('id', RecipeItems::where(['recipe_id' => $recipe_id, 'type' => $type])->lists('item_id'))->orderBy('title', 'ASC')->lists('title', 'id');
            if($select_items) {
                return view('RecipeItems.create_item')->with(array(
                    'title' => $this->title,
                    'recipe' => $recipe,
                    'items' => $select_items,
                    'items_units' => $items_units,
                    'type' => $type
                ));
            } else {
                Session::flash('flash_message', 'It looks like you have used all possible products in your recipe.');
                return Redirect::action('RecipeItemsController@index', $recipe->id);
            }
        }
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
    public function store(Request $request){
        $input = $request->all();
        $recipe = Recipes::findOrFail($input['recipe_id']);
        if($input['type'] == 'recipe'){
            $item = Recipes::findOrFail($input['sub_recipe']);
        } else {
            $item = Items::findOrFail($input['item_id']);
        }
        RecipeItems::create($input);
        Helper::add(DB::getPdo()->lastInsertId(), 'added '.$input['type'].' '.$item->title.' (ID '.$recipe->id.') to recipe '.$recipe->title.' (ID '.$recipe->id.')');
        Session::flash('flash_message', $this->title.' successfully added!');

        return Redirect::action('RecipeItemsController@index', $request->get('recipe_id'));
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
	public function edit($recipe_id, $type)
	{

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
        $RecipeItems = RecipeItems::findOrFail($id);
        $recipe = Recipes::findOrFail($RecipeItems->recipe_id);
        if($RecipeItems->type == 'recipe'){
            $item = Recipes::findOrFail($RecipeItems->sub_recipe);
        } else {
            $item = Items::findOrFail($RecipeItems->item_id);
        }
        $RecipeItems->delete();
        Helper::add($id, 'deleted '.$RecipeItems->type.' '.$item->title.' (ID '.$RecipeItems->id.') from recipe '.$recipe->title.' (ID '.$recipe->id.')');
        Session::flash('flash_message', $this->title.' successfully deleted!');

        return Redirect::action('RecipeItemsController@index', ['recipe_id' => $recipe->id]);
    }

}
