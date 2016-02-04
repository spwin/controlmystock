<?php namespace App\Http\Controllers;

use Helper;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Files;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MenusController extends Controller {

    private $title = 'Menu';
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('Menus.index')->with(array(
            'title' => $this->title,
            'items' => Menu::orderBy('title', 'ASC')->get()
        ));
	}

    function _import_csv($path, $filename)
    {
        $csv_config = [
            'start_from_line' => 11,
            'number_row' => 1,
            'title_row' => 2,
            'quantity_row' => 3,
            'price_row' => 4
        ];
        $csv = $path .'/'. $filename;
        $content = array_map('str_getcsv', file($csv));
        $data = [];
        for($i = $csv_config['start_from_line']; $i < count($content); $i++){
            if(is_numeric($content[$i][$csv_config['number_row']])) {
                $data[] = [
                    'number' => $content[$i][$csv_config['number_row']],
                    'title' => $content[$i][$csv_config['title_row']],
                    'quantity' => $content[$i][$csv_config['quantity_row']],
                    'price' => $content[$i][$csv_config['price_row']],
                ];
            }
        }
        return $data;
    }

    function postUpload (Request $request)
    {
        $file = $request->file('file');
        $input = $request->all();
        $validator = Validator::make(
            [
                'file'      => $file,
                'extension' => strtolower($file ? $file->getClientOriginalExtension() : 'sss'),
            ],
            [
                'file'          => 'required',
                'extension'      => 'required|in:csv',
            ]
        );
        if ($validator->fails()) {
            return Redirect::action('MenusController@index')->withErrors($validator);
        }
        $data = [];
        if (Input::hasFile('file')){
            if(array_key_exists('file', $input)){
                if($input['file']->isValid()){
                    $filename = 'aloha_'.date('Y_m_d_His').'.'.$input['file']->getClientOriginalExtension();
                    $path = storage_path().'/upload';
                    $input['file']->move($path, $filename);
                    $file = Files::create(['filename' => $filename]);
                    $data = $this->_import_csv($path, $file->filename);
                }
            }
        }
        return $data;
    }

    public function uploadMenu (Request $request)
    {
        $data = $this->postUpload($request);
        if(is_array($data)) {
            $before = Menu::count();
            foreach ($data as $item) {
                Menu::updateOrCreate(
                    ['number' => $item['number']],
                    ['number' => $item['number'], 'title' => $item['title'], 'price' => $item['price']]
                );
            }
            $difference = Menu::count() - $before;
            Helper::add('', 'uploaded menu items import file');
            if($difference > 0) {
                Session::flash('flash_message', $difference . ' ' . $this->title . ' items successfully imported!');
            } else {
                Session::flash('flash_message', 'No new ' . $this->title . ' items imported!');
            }
            return Redirect::action('MenusController@index');
        } else {
            return $data;
        }
    }

    public function assign($id)
    {
        $item = Menu::findOrFail($id);
        return view('Menus.assign')->with(array(
            'title' => $this->title,
            'item' => $item
        ));
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
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
		//
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
        $menu = Menu::findOrFail($id);
        $menu->delete();
        Helper::add($id, 'deleted menu item');
        Session::flash('flash_message', $this->title.' item successfully deleted!');
        return Redirect::action('MenusController@index');
	}

}
