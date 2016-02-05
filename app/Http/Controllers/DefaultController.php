<?php namespace App\Http\Controllers;

use App\Models\Items;
use App\Models\Purchases;
use App\Models\SaleItems;
use App\Models\Sales;
use App\Models\StockItem;
use Helper;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class DefaultController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $last_period = Helper::lastPeriodId();
        $current_period = Helper::currentPeriodId();
        $item_purchases = [];
        $last_stock = [];
        $current_stock = [];
        $item_sales = [];
        $items = [];
        if($last_period){
            $purchases = Purchases::orderBy('date_created', 'ASC')->where(['stock_period_id' => $last_period])->get();
            foreach($purchases as $purchase){
                foreach($purchase->purchases()->get() as $itemPurchase){
                    if(array_key_exists($itemPurchase->item_id, $item_purchases)){
                        $item_purchases[$itemPurchase->item_id]['value'] += $itemPurchase->value;
                        $item_purchases[$itemPurchase->item_id]['occurrences']++;
                        $item_purchases[$itemPurchase->item_id]['price'] += $itemPurchase->value == 0 ? 0 : $itemPurchase->price/$itemPurchase->value;
                    } else {
                        $item_purchases[$itemPurchase->item_id]['value'] = $itemPurchase->value;
                        $item_purchases[$itemPurchase->item_id]['price'] = $itemPurchase->value == 0 ? 0 : $itemPurchase->price/$itemPurchase->value;
                        $item_purchases[$itemPurchase->item_id]['occurrences'] = 1;
                    }
                }
            }
            foreach($item_purchases as $key => $purchase){
                $item_purchases[$key]['price'] = round($purchase['price']/$purchase['occurrences'], 2);
            }

            $last_stock_items = StockItem::where(['stock_period_id' => $last_period])->get();
            foreach($last_stock_items as $si){
                if(array_key_exists($si->item_id, $last_stock)) {
                    $last_stock[$si->item_id]+= $si->stock;
                } else {
                    $last_stock[$si->item_id] = $si->stock;
                }
            }

            $current_stock_items = StockItem::where(['stock_period_id' => $current_period])->get();
            foreach($current_stock_items as $si){
                if(array_key_exists($si->item_id, $current_stock)) {
                    $current_stock[$si->item_id] += $si->stock;
                } else {
                    $current_stock[$si->item_id] = $si->stock;
                }
            }

            $sales = Sales::where(['stock_period_id' => $last_period])->get();
            foreach($sales as $sale){
                foreach($sale->sales()->get() as $sale_item){
                    $menu = $sale_item->menu()->first();
                    if($menu){
                        if($menu->type == 'item'){
                            if(array_key_exists($menu->item_id, $item_sales)){
                                $item_sales[$menu->item_id] += ($menu->value * $sale_item->quantity);
                            } else {
                                $item_sales[$menu->item_id] = ($menu->value * $sale_item->quantity);
                            }
                        } elseif ($menu->type == 'recipe'){

                        }
                    }
                }
            }
        }
        $all_items = Items::all();
        $variance = 0;
        foreach($all_items as $item){
            //$items[$item->id]['object'] = $item;
            $items[$item->id]['title'] = $item->title;
            $items[$item->id]['last_stock'] = array_key_exists($item->id, $last_stock) ? $last_stock[$item->id] : 0;
            $items[$item->id]['current_stock'] = array_key_exists($item->id, $current_stock) ? $current_stock[$item->id] : 0;
            $items[$item->id]['purchases'] = array_key_exists($item->id, $item_purchases) ? $item_purchases[$item->id] : ['value' => 0, 'price' => 0, 'occurrences' => 0];
            $items[$item->id]['sales'] = array_key_exists($item->id, $item_sales) ? $item_sales[$item->id] : 0;
            $items[$item->id]['must_stock'] = $items[$item->id]['last_stock'] + $items[$item->id]['purchases']['value'] - $items[$item->id]['sales'];
            $items[$item->id]['stock_difference'] = $items[$item->id]['current_stock'] - $items[$item->id]['must_stock'];
            $items[$item->id]['variance'] = round(($items[$item->id]['current_stock'] - ($items[$item->id]['last_stock'] + $items[$item->id]['purchases']['value'] - $items[$item->id]['sales'])) * $items[$item->id]['purchases']['price'], 2);
            $variance += $items[$item->id]['variance'];
        }
        echo '<pre style="display: none;">';
        print_r($items);
        echo '</pre>';
		return view('Default.index')->with(array(
            'last_period' => $last_period,
            'last_stock_summary_items' => $items
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
		//
	}

}
