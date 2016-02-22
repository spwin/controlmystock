<?php namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Purchases;
use Helper;
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
        Helper::add($result->id, 'created item '.$input['title']);
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
        Helper::add($Items->id, 'edited item '.$input['title']);
        Session::flash('flash_message', $this->title.' successfully added!');

        return Redirect::action('ItemsController@index');
    }

    public function destroy($id)
    {
        $Items = Items::findOrFail($id);

        Helper::add($Items->id, 'deleted item '.$Items->title);

        if($Items->menus()->count() > 0 ){
            Session::flash('flash_message', $this->title.' is assigned to menu and cannot be deleted, unassign first!');
        } else {
            Session::flash('flash_message', $this->title.' successfully deleted!');
            $Items->delete();
        }

        return Redirect::action('ItemsController@index');
    }

    public function prices(){
        $items = Items::whereRaw('not exists (select 1 from item_purchases where item_purchases.item_id = items.id)')->orderBy('items.price', 'ASC')->orderBy('items.category_id', 'ASC')->orderBy('items.title', 'ASC')->get();
        return view('Items.prices')->with(array(
            'title' => 'Items without price',
            'items' => $items
        ));
    }

    public function setPrice($id){
        $item = Items::findOrFail($id);
        return view('Items.setprice')->with(array(
            'title' => 'Set price for Item',
            'item' => $item
        ));
    }

    public function updatePrice($id, Request $request){
        $item = Items::findOrFail($id);
        $input = $request->all();
        $item->fill($input)->save();
        Helper::add($item->id, 'set price for item '.$item->title);
        Session::flash('flash_message', $this->title.' price added!');

        return Redirect::action('ItemsController@prices');
    }

    public function show($id){
        $item = Items::with('recipes')->with('purchases')->findOrFail($id);
        $history = History::where('message', 'LIKE', '%(ID '.$id.')%')->where('action', '=', 'App\Http\Controllers\StockCheckController@store ')->get();
        echo '<pre>';
        echo $history->count();
        echo 'id: '.$id;
        echo '</pre>';
        return view('Items.show')->with(array(
            'title' => $this->title,
            'item' => $item,
            'history' => $history
        ));
    }

}
