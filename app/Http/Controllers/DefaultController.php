<?php namespace App\Http\Controllers;

use App\Models\ItemCategories;
use App\Models\ItemPurchases;
use App\Models\Items;
use App\Models\Menu;
use App\Models\Purchases;
use App\Models\Recipes;
use App\Models\SaleItems;
use App\Models\Sales;
use App\Models\StockItem;
use App\Models\Wastes;
use Helper;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class DefaultController extends Controller {


    function countUsageFromRecipe($recipe, $array = []){
        foreach($recipe->items()->get() as $item){
            if($item->type == 'item'){
                if(array_key_exists($item->item_id, $array)){
                    $array[$item->item_id] += $item->value;
                } else {
                    $array[$item->item_id] = $item->value;
                }
            } elseif($item->type == 'recipe'){
                return $this->countUsageFromRecipe($item->subrecipe()->first(), $array);
            }
        }
        return $array;
    }

    function rand_color() {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    function alter_brightness($colourstr, $steps) {
        $colourstr = str_replace('#','',$colourstr);
        $rhex = substr($colourstr,0,2);
        $ghex = substr($colourstr,2,2);
        $bhex = substr($colourstr,4,2);

        $r = hexdec($rhex);
        $g = hexdec($ghex);
        $b = hexdec($bhex);

        $r = max(0,min(255,$r + $steps));
        $g = max(0,min(255,$g + $steps));
        $b = max(0,min(255,$b + $steps));

        return '#'.dechex($r).dechex($g).dechex($b);
    }
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
        $item_wastes = [];
        $items = [];
        $wastage = [];
        $items_without_price = 0;
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
                $item_purchases[$key]['price'] = $purchase['price']/$purchase['occurrences'];
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
                            $recipe = $menu->recipe()->first();
                            if($recipe){
                                $usage = $this->countUsageFromRecipe($recipe);
                                foreach($usage as $key => $use){
                                    if(array_key_exists($key, $item_sales)){
                                        $item_sales[$key] += $use;
                                    } else {
                                        $item_sales[$key] = $use;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $wastes = Wastes::where(['stock_period_id' => $last_period])->get();
            foreach($wastes as $waste){
                if(array_key_exists($waste->reason()->first()->id, $wastage)){
                    $wastage[$waste->reason()->first()->id]['value'] = $wastage[$waste->reason()->first()->id]['value']+1;
                } else {
                    $color = $this->rand_color();
                    $wastage[$waste->reason()->first()->id] = [
                        'value' => 1,
                        'color' => $color,
                        'highlight' => $this->alter_brightness($color, 20),
                        'label' => $waste->reason()->first()->reason
                    ];
                }
                if($waste->type == 'item'){
                    if(array_key_exists($waste->item_id, $item_wastes)){
                        $item_wastes[$waste->item_id] += $waste->value;
                    } else {
                        $item_wastes[$waste->item_id] = $waste->value;
                    }
                } elseif($waste->type == 'recipe'){
                    $recipe = $waste->recipe()->first();
                    if($recipe){
                        $usage = $this->countUsageFromRecipe($recipe);
                        foreach($usage as $key => $use){
                            if(array_key_exists($key, $item_wastes)){
                                $item_wastes[$key] += ($waste->recipe_count * $use);
                            } else {
                                $item_wastes[$key] = ($waste->recipe_count * $use);
                            }
                        }
                    }
                } elseif($waste->type == 'menu'){
                    $menu = $waste->menu()->first();
                    if($menu){
                        if($menu->type == 'item'){
                            if(array_key_exists($menu->item_id, $item_wastes)){
                                $item_wastes[$menu->item_id] += ($waste->menu_count * $menu->value);
                            } else {
                                $item_wastes[$menu->item_id] = ($waste->menu_count * $menu->value);
                            }
                        } elseif ($menu->type == 'recipe'){
                            $recipe = $menu->recipe()->first();
                            if($recipe){
                                $usage = $this->countUsageFromRecipe($recipe);
                                foreach($usage as $key => $use){
                                    if(array_key_exists($key, $item_wastes)){
                                        $item_wastes[$key] += ($waste->menu_count * $use);
                                    } else {
                                        $item_wastes[$key] = ($waste->menu_count * $use);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $all_items = Items::all();
        $variance = 0;
        $count = 0;
        foreach($all_items as $item){
            if(!array_key_exists($item->category()->first()->id, $items)){
                $items[$item->category()->first()->id]['category'] = $item->category()->first()->title;
                $items[$item->category()->first()->id]['variance'] = 0;
                $items[$item->category()->first()->id]['items'] = [];
            }
            $current_item = [];
            //$items[$item->id]['object'] = $item;
            $current_item['title'] = $item->title;
            $current_item['units'] = $item->units()->where(['default' => 1])->first()->unit()->first()->title;
            $current_item['wastage'] = array_key_exists($item->id, $item_wastes) ? $item_wastes[$item->id] : 0;
            $current_item['last_stock'] = array_key_exists($item->id, $last_stock) ? $last_stock[$item->id] : 0;
            $current_item['current_stock'] = array_key_exists($item->id, $current_stock) ? $current_stock[$item->id] : 0;
            if(array_key_exists($item->id, $item_purchases)){
                $current_item['purchases'] = $item_purchases[$item->id];
            } else {
                $price = 0;
                $item_price = ItemPurchases::where(['item_id' => $item->id])->orderBy('created_at', 'DESC')->first();
                if($item_price){
                    $value = $item->value;
                    if($value == 0) $value = 1;
                    $price = $item_price->price/$value;
                } else {
                    if($item->price){
                        $price = $item->price;
                    } else {
                        $items_without_price++;
                    }
                }
                $current_item['purchases'] = ['value' => 0, 'price' => $price, 'occurrences' => 0];
            }
            $current_item['sales'] = array_key_exists($item->id, $item_sales) ? $item_sales[$item->id] : 0;
            $current_item['must_stock'] = $current_item['last_stock'] + $current_item['purchases']['value'] - $current_item['sales'] - $current_item['wastage'];
            $current_item['stock_difference'] = $current_item['current_stock'] - $current_item['must_stock'];
            $current_item['variance'] = round($current_item['stock_difference'] * $current_item['purchases']['price'], 2);
            $variance += $current_item['variance'];
            $items[$item->category()->first()->id]['variance'] += $current_item['variance'];
            $items[$item->category()->first()->id]['items'][$item->id] = $current_item;
            $count++;
        }
        $summary_stock = Items::select('count(*)')->whereRaw('not exists (select 1 from stock_items where stock_items.stock_period_id = '.$current_period.' and stock_items.item_id = items.id)')->count();
		$summary_invoices = Purchases::where(['stock_period_id' => $current_period])->count();
        $summary_sales = Sales::where(['stock_period_id' => $current_period])->count();
        $summary_menu = Menu::where(['checked' => 0])->count();
        return view('Default.index')->with(array(
            'last_period' => $last_period,
            'last_stock_summary_items' => $items,
            'variance' => $variance,
            'items' => $items,
            'count' => $count,
            'wastage' => $wastage,
            'summary' => ['stock' => $summary_stock, 'invoices' => $summary_invoices, 'sales' => $summary_sales, 'menu' => $summary_menu, 'no_price' => $items_without_price]
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
