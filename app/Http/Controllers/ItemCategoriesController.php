<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\ItemCategories;
use App\Models\Items;
use App\User;
use Helper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Http\Request;

class ItemCategoriesController extends Controller {

    private $title = 'Item Categories';

    public function index(){
        return view('ItemCategories.index')->with([
            'title' => $this->title,
            'categories' => ItemCategories::with('children', 'parent')->get(),
            'root_categories' => ItemCategories::with('children', 'parent')->whereNull('parent_id')->get()
        ]);
    }

    public function create($parent){
        return view('ItemCategories.create')->with(array(
            'title' => $this->title,
            'categories' => ItemCategories::lists('title', 'id'),
            'parent' => $parent
        ));
    }

    public function store(Request $request){
        $this->validate($request, [
            'title' => 'required|max:100'
        ]);
        $input = $request->all();
        if(!$input['parent_id']){
            $input['parent_id'] = 'NULL';
        }
        DB::statement( 'INSERT INTO item_categories (parent_id, title, updated_at, created_at) VALUES ('.$input['parent_id'].', "'.$input['title'].'", NOW(), NOW())' );
        Helper::add(DB::getPdo()->lastInsertId(), 'created new items category '.$input['title']);
        //ItemCategories::create(['parent_id' => $input['parent_id'], 'title' => $input['title']]);
        Session::flash('flash_message', $this->title.' successfully added!');

        return Redirect::action('ItemCategoriesController@index');
    }

    public function edit($id)
    {
        $ItemCategories = ItemCategories::findOrFail($id);
        return view('ItemCategories.edit')->with(array(
            'title' => $this->title,
            'ItemCategories' => $ItemCategories,
            'categories' => ItemCategories::where('id', '!=', $id)->lists('title', 'id')
        ));
    }

    public function update($id, Request $request)
    {
        $ItemCategories = ItemCategories::findOrFail($id);

        $this->validate($request, [
            'title' => 'required|max:100'
        ]);
        $input = $request->all();
        DB::statement( 'UPDATE item_categories SET parent_id = '.($input['parent_id'] ? $input['parent_id'] : 'NULL').', title = "'.$input['title'].'", updated_at = NOW() WHERE id = '.$ItemCategories->id );
        Helper::add(DB::getPdo()->lastInsertId(), 'updated items category '.$input['title']);
        Session::flash('flash_message', $this->title.' successfully updated!');

        return Redirect::action('ItemCategoriesController@index');
    }

    public function destroy($id)
    {
        $ItemCategories = ItemCategories::findOrFail($id);

        $items = Items::where(['category_id' => $id])->get();
        Helper::add($id, 'deleted items category '.$ItemCategories->title);
        if(count($items) > 0){
            $category = ItemCategories::where(['title' => 'Uncategorized'])->first();
            if(!$category){
                $category = ItemCategories::create(['title' => 'Uncategorized']);
            }
            Items::where(['category_id' => $id])->update(['category_id' => $category->id]);
        }
        if($ItemCategories->title == 'Uncategorized' && count($items) > 0){} else {
            $ItemCategories->delete();
        }

        Session::flash('flash_message', $this->title.' successfully deleted!');

        return Redirect::action('ItemCategoriesController@index');
    }
}
