<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\UnitGroups;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class UnitGroupsController extends Controller
{
    private $title = 'Unit Group';

    public function index(){
        return view('UnitGroups.index')->with([
            'title' => $this->title,
            'groups' => UnitGroups::all()
        ]);
    }

    public function create(){
        return view('UnitGroups.create')->with(array(
            'title' => $this->title
        ));
    }

    public function store(Request $request){
        $this->validate($request, [
            'title' => 'required|unique:unit_groups|max:100'
        ]);
        $input = $request->all();

        UnitGroups::create($input);
        Session::flash('flash_message', $this->title.' successfully added!');

        return Redirect::action('UnitGroupsController@index');
    }

    public function edit($id)
    {
        $UnitGroup = UnitGroups::findOrFail($id);
        return view('UnitGroups.edit')->with(array(
            'title' => $this->title,
            'UnitGroup' => $UnitGroup
        ));
    }

    public function update($id, Request $request)
    {
        $UnitGroup = UnitGroups::findOrFail($id);

        $this->validate($request, [
            'title' => 'required|unique:unit_groups|max:100'
        ]);

        $input = $request->all();

        $UnitGroup->fill($input)->save();

        Session::flash('flash_message', $this->title.' successfully updated!');

        return Redirect::action('UnitGroupsController@index');
    }

    public function destroy($id)
    {
        $UnitGroup = UnitGroups::findOrFail($id);
        if($UnitGroup->disable_delete){
            Session::flash('flash_message', 'This '.$this->title.' is not deletable!');
        } else {
            $UnitGroup->delete();
            Session::flash('flash_message', $this->title.' successfully deleted!');
        }

        return Redirect::action('UnitGroupsController@index');
    }
}