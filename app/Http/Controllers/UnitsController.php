<?php namespace App\Http\Controllers;

use Helper;
use App\Http\Requests;
use App\Models\Units;
use App\Models\UnitGroups;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class UnitsController extends Controller {

    private $title = 'Units';
    private function getGroupDefaults(){
        $GroupsDefaults = DB::table('unit_groups')
            ->select(DB::raw('unit_groups.id, units.title'))
            ->join('units', 'unit_groups.id', '=', 'units.group_id')
            ->where('units.default', '=', 1)
            ->groupBy('unit_groups.id')
            ->get();
        $groups_defaults = [];
        foreach($GroupsDefaults as $key => $group){
            $groups_defaults[$group->id] = $group->title;
        }
        return $groups_defaults;
    }

    public function index(){
        return view('Units.index')->with([
            'title' => $this->title,
            'items' => Units::orderBy('group_id')->get(),
            'defaults' => $this->getGroupDefaults()
        ]);
    }

    public function create(){
        return view('Units.create')->with(array(
            'title' => $this->title,
            'groups' => UnitGroups::lists('title', 'id'),
            'defaults' => $this->getGroupDefaults()
        ));
    }

    public function store(Request $request){
        $this->validate($request, [
            'title' => 'required|unique:units|max:100'
        ]);
        $input = $request->all();
        $group = UnitGroups::findOrFail($input['group_id']);
        if(array_key_exists('default', $input)){
            DB::table('units')->where(['default' => 1, 'group_id' => $input['group_id']])->update(['default' => 0]);
            $input['default'] = 1;
            Units::create($input);
            Helper::add(DB::getPdo()->lastInsertId(), 'added new unit '.$input['title'].' (ID '.DB::getPdo()->lastInsertId().')');
            Helper::add(DB::getPdo()->lastInsertId(), 'changed default unit to '.$input['title'].' (ID '.DB::getPdo()->lastInsertId().') in group '.$group->title.' (ID '.$group->id.')');
            Units::where(['group_id' => $input['group_id']])->update(['factor' => DB::raw('factor/'.$input['factor'])]);
        } else {
            Units::create($input);
            Helper::add(DB::getPdo()->lastInsertId(), 'added new unit '.$input['title'].' (ID '.DB::getPdo()->lastInsertId().')');
        }
        Session::flash('flash_message', $this->title.' successfully added!');

        return Redirect::action('UnitsController@index');
    }

    public function edit($id)
    {
        return view('Units.edit')->with(array(
            'title' => $this->title,
            'groups' => UnitGroups::lists('title', 'id'),
            'defaults' => $this->getGroupDefaults(),
            'Units' => Units::findOrFail($id)
        ));
    }

    public function update($id, Request $request)
    {
        $Units = Units::findOrFail($id);
        $this->validate($request, [
            'title' => 'required|max:100'
        ]);
        $input = $request->all();
        Helper::add($id, 'edited unit '.$Units->title.' (ID '.$Units->id.')');
        if(array_key_exists('default', $input)){
            $group = UnitGroups::findOrFail($input['group_id']);
            Units::where(['default' => 1, 'group_id' => $input['group_id']])->where('id', '!=', $id)->update(['default' => 0]);
            Units::where(['group_id' => $input['group_id']])->update(['factor' => DB::raw('factor/'.$input['factor'])]);
            Helper::add($id, 'changed default unit to '.$Units->title.' (ID '.$Units->id.') in group '.$group->title.' (ID '.$group->id.')');
            $input['default'] = 1;
        }
        $Units->fill($input)->save();
        Session::flash('flash_message', $this->title.' successfully added!');

        return Redirect::action('UnitsController@index');
    }

    public function destroy($id)
    {
        $Units = Units::findOrFail($id);
        if($Units->disable_delete){
            Session::flash('flash_message', 'This '.$this->title.' is not deletable!');
        } else {
            Helper::add($id, 'deleted unit '.$Units->title.' (ID '.$Units->id.')');
            if($Units->default){
                $first = Units::where('id', '!=', $id)->where(['group_id' => $Units->group_id])->first();
                if($first){
                    $first->update(['default' => 1]);
                    $group = UnitGroups::findOrFail($first->group_id);
                    Units::where(['group_id' => $Units->group_id])->update(['factor' => DB::raw('factor/'.$first->factor)]);
                    Helper::add($id, 'changed default unit to '.$Units->title.' (ID '.$Units->id.') in group '.$group->title.' (ID '.$group->id.')');
                }
            }
            $Units->delete();
            Session::flash('flash_message', $this->title.' successfully deleted!');
        }

        return Redirect::action('UnitsController@index');
    }

}
