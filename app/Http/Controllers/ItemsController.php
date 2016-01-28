<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Units;
use App\Models\Items;
use App\Models\ItemCategories;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class ItemsController extends Controller {

    private $title = 'Items';

    public function index(){
        $search = Input::get('q');
        $items = $search ? Items::where('title', 'LIKE', '%'.$search.'%')->orderBy('category_id')->get() :  Items::orderBy('category_id')->get();
        return view('Items.index')->with([
            'title' => $this->title,
            'items' => $items,
            'search' => $search
        ]);
    }

    public function create(){
        $name = Input::get('name');
        $last_tree_categories = ItemCategories::select('item_categories.*')->leftJoin('item_categories AS c2', 'item_categories.id', '=', 'c2.parent_id')->whereNull('c2.id')->lists('title', 'id');
        foreach($last_tree_categories as $key => $category){
            $last_tree_categories[$key] = $last_tree_categories[$key]." (id:{$key})";
        }
        return view('Items.create')->with(array(
            'title' => $this->title,
            'categories' => $last_tree_categories,
            'name' => $name
        ));
    }

    public function store(Request $request){
        $this->validate($request, [
            'title' => 'required|unique:items|max:100'
        ]);
        $input = $request->all();
        $result = Items::create($input);
        Session::flash('flash_message', $this->title.' successfully added!');
        if($result) {
            return Redirect::action('ItemUnitsController@create', $result->id);
        } else {
            return Redirect::action('ItemsController@index');
        }
    }

    public function edit($id)
    {
        $last_tree_categories = ItemCategories::select('item_categories.*')->leftJoin('item_categories AS c2', 'item_categories.id', '=', 'c2.parent_id')->whereNull('c2.id')->lists('title', 'id');
        foreach($last_tree_categories as $key => $category){
            $last_tree_categories[$key] = $last_tree_categories[$key]." (id:{$key})";
        }
        return view('Items.edit')->with(array(
            'title' => $this->title,
            'categories' => $last_tree_categories,
            'Items' => Items::findOrFail($id),
            'units' => Units::lists('title', 'id')
        ));
    }

    public function update($id, Request $request)
    {
        $Items = Items::findOrFail($id);
        $this->validate($request, [
            'title' => 'required|max:100'
        ]);
        $input = $request->all();
        $Items->fill($input)->save();
        Session::flash('flash_message', $this->title.' successfully added!');

        return Redirect::action('ItemsController@index');
    }

    public function destroy($id)
    {
        $Items = Items::findOrFail($id);

        $Items->delete();

        Session::flash('flash_message', $this->title.' successfully deleted!');

        return Redirect::action('ItemsController@index');
    }

}
