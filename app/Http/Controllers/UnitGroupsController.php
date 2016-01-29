<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Helper;
use App\Models\UnitGroups;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
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
        Helper::add(DB::getPdo()->lastInsertId(), 'added new unit group '.$input['title'].' (ID '.DB::getPdo()->lastInsertId().')');
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
        Helper::add($id, 'edited unit group '.$UnitGroup->title.' (ID '.$id.')');
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
            Helper::add($id, 'deleted unit group '.$UnitGroup->title.' (ID '.$id.')');
            Session::flash('flash_message', $this->title.' successfully deleted!');
        }

        return Redirect::action('UnitGroupsController@index');
    }
}