<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\ItemCategories;
use App\Models\Items;
use App\Models\PurchaseCategory;
use App\User;
use Helper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Http\Request;

class PurchaseCategoriesController extends Controller {

    private $title = 'Purchase Categories';

    public function index(){
        return view('PurchaseCategories.index')->with([
            'title' => $this->title,
            'categories' => PurchaseCategory::all()
        ]);
    }

    public function create(){
        return view('PurchaseCategories.create')->with(array(
            'title' => $this->title
        ));
    }

    public function store(Request $request){
        $this->validate($request, [
            'title' => 'required|max:100'
        ]);
        $input = $request->all();
        PurchaseCategory::create($input);
        Helper::add(DB::getPdo()->lastInsertId(), 'created new purchase category '.$input['title']);
        Session::flash('flash_message', $this->title.' successfully added!');

        return Redirect::action('PurchaseCategoriesController@index');
    }

    public function edit($id)
    {
        $category = PurchaseCategory::findOrFail($id);
        return view('PurchaseCategories.edit')->with(array(
            'title' => $this->title,
            'category' => $category
        ));
    }

    public function update($id, Request $request)
    {
        $category = PurchaseCategory::findOrFail($id);
        $this->validate($request, [
            'title' => 'required|max:100'
        ]);
        $input = $request->all();
        $category->fill($input)->save();
        Helper::add(DB::getPdo()->lastInsertId(), 'updated purchase category '.$input['title']);
        Session::flash('flash_message', $this->title.' successfully updated!');

        return Redirect::action('PurchaseCategoriesController@index');
    }

    public function destroy($id)
    {
        $category = PurchaseCategory::findOrFail($id);
        $category->delete();

        Session::flash('flash_message', $this->title.' successfully deleted!');

        return Redirect::action('PurchaseCategoriesController@index');
    }
}
