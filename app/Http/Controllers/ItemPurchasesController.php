<?php namespace App\Http\Controllers;

use Helper;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\ItemPurchases;
use App\Models\Items;
use App\Models\ItemUnits;
use App\Models\Purchases;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class ItemPurchasesController extends Controller
{

    private $title = 'Invoice';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($id)
    {
        $purchase = Purchases::findOrFail($id);
        $items = ItemPurchases::where(['purchase_id' => $id])->get();
        return view('ItemPurchases.index')->with(array(
            'title' => $this->title,
            'purchase' => $purchase,
            'items' => $items
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($purchase_id, $type)
    {
        $purchase = Purchases::findOrFail($purchase_id);
        if ($type == 'item') {
            $items = ItemUnits::orderBy('default', 'DESC')->get();
            $items_units = [];
            foreach ($items as $item) {
                $items_units['list'][$item->item()->first()->id][] = ['id' => $item->id, 'title' => $item->unit()->first()->title];
                $items_units['php_list'][$item->item()->first()->id][$item->id] = $item->unit()->first()->title;
                $items_units['factors'][$item->id] = $item->factor;
                $items_units['item_to_unit'][$item->id] = $item->unit()->first()->id;
            }
            $select_items = Items::orderBy('title', 'ASC')->lists('title', 'id');
            if ($select_items) {
                return view('ItemPurchases.create_item')->with(array(
                    'title' => $this->title,
                    'purchase' => $purchase,
                    'items' => $select_items,
                    'items_units' => $items_units,
                    'type' => $type
                ));
            } else {
                Session::flash('flash_message', 'It looks like you have used all possible products in your invoice.');
                return Redirect::action('ItemPurchasesController@index', $purchase->id);
            }
        } else {
            return view('ItemPurchases.create_custom')->with(array(
                'title' => $this->title,
                'purchase' => $purchase,
                'type' => $type
            ));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $purchase = Purchases::findOrFail($input['purchase_id']);
        if ($input['type'] == 'item') {
            ItemPurchases::create($input);
        } else {
            $this->validate($request, [
                'item_custom' => 'unique:item_purchases'
            ]);
            $input['value'] = $input['value_entered'];
            ItemPurchases::create($input);
        }
        //RecipeItems::create($input);
        //Helper::add(DB::getPdo()->lastInsertId(), 'added '.$input['type'].' '.$item->title.' (ID '.$recipe->id.') to recipe '.$recipe->title.' (ID '.$recipe->id.')');
        //Session::flash('flash_message', $this->title.' successfully added!');

        return Redirect::action('ItemPurchasesController@index', $request->get('purchase_id'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $item_purchase = ItemPurchases::findOrFail($id);
        $purchase = $item_purchase->purchase()->first();
        $type = $item_purchase->type;
        if ($type == 'item') {
            $items = ItemUnits::orderBy('default', 'DESC')->get();
            $items_units = [];
            foreach ($items as $item) {
                $items_units['list'][$item->item()->first()->id][] = ['id' => $item->id, 'title' => $item->unit()->first()->title];
                $items_units['php_list'][$item->item()->first()->id][$item->id] = $item->unit()->first()->title;
                $items_units['factors'][$item->id] = $item->factor;
                $items_units['item_to_unit'][$item->id] = $item->unit()->first()->id;
            }
            $select_items = Items::orderBy('title', 'ASC')->lists('title', 'id');
            if ($select_items) {
                return view('ItemPurchases.edit_item')->with(array(
                    'title' => $this->title,
                    'purchase' => $purchase,
                    'items' => $select_items,
                    'items_units' => $items_units,
                    'type' => $type,
                    'item' => $item_purchase
                ));
            } else {
                Session::flash('flash_message', 'It looks like you have used all possible products in your invoice.');
                return Redirect::action('ItemPurchasesController@index', $purchase->id);
            }
        } else {
            return view('ItemPurchases.edit_custom')->with(array(
                'title' => $this->title,
                'purchase' => $purchase,
                'type' => $type,
                'item' => $item_purchase
            ));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id, Request $request)
    {
        $Items = ItemPurchases::findOrFail($id);
        $input = $request->all();
        if ($input['type'] == 'item') {
            $Items->fill($input)->save();
        } else {
            $input['value'] = $input['value_entered'];
            $Items->fill($input)->save();
        }
        Helper::add($Items->id, 'edited invoice item ');
        Session::flash('flash_message', $this->title.' successfully added!');

        return Redirect::action('ItemPurchasesController@index', $request->get('purchase_id'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $ItemPurchases = ItemPurchases::findOrFail($id);
        Helper::add($ItemPurchases->id, 'deleted purchase ID ' . $ItemPurchases->id);
        $ItemPurchases->delete();
        Session::flash('flash_message', $this->title . ' successfully deleted!');
        return Redirect::action('ItemPurchasesController@index', $ItemPurchases->purchase_id);
    }

    public function generate($id, Request $request)
    {
        $input = $request->all();
        $this->validate($request, [
            'total_price' => 'numeric',
            'total_vat' => 'numeric'
        ]);
        $items = ItemPurchases::where(['purchase_id' => $id])->get();
        $total_price = 0;
        $total_vat = 0;
        foreach($items as $item){
            $total_price += $item->price;
            $total_vat += $item->vat;
        }
        $item = ItemPurchases::where(['item_custom' => 'Other items', 'purchase_id' => $id])->first();
        if($total_price != $input['total_price'] || $total_vat != $input['total_vat']) {
            if (count($item) > 0) {
                $item->increment('price', round($input['total_price'] - $total_price, 2));
                $item->increment('vat', round($input['total_vat'] - $total_vat, 2));
                $item->save();
            } else {
                $data = [
                    'purchase_id' => $id,
                    'type' => 'custom',
                    'item_custom' => 'Other items',
                    'unit_custom' => 'all',
                    'value_entered' => 1,
                    'value' => 1,
                    'price' => round(floatval($input['total_price']) - $total_price, 2),
                    'vat' => round(floatval($input['total_vat']) - $total_vat,  2)
                ];
                ItemPurchases::create($data);
            }
            Session::flash('flash_message', 'Custom '.$this->title .' successfully generated!');
        } else {
            Session::flash('flash_message', 'Change total values to generate custom item!');
        }
        return Redirect::action('ItemPurchasesController@index', $id);
    }

}
