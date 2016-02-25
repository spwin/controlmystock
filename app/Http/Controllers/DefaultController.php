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
use App\Models\StockPeriods;
use App\Models\Wastes;
use Helper;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

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
        $last_period = Helper::defaultPeriodId();
        $current_period = Helper::periodAfterId($last_period);
        $item_purchases = [];
        $last_stock = [];
        $current_stock = [];
        $item_sales = [];
        $item_wastes = [];
        $items = [];
        $wastage = [];
        $sales_chart = [];
        $items_without_price = 0;
        $period = null;
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
                foreach($sale->sales()->orderBy('quantity', 'DESC')->get() as $sale_item){
                    $menu = $sale_item->menu()->first();
                    if($menu){
                        $color = $this->rand_color();
                        $sales_chart[$menu->id] = [
                            'value' => $sale_item->quantity,
                            'color' => $color,
                            'highlight' => $this->alter_brightness($color, 20),
                            'label' => $menu->title
                        ];
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
                                        $item_sales[$key] += ($sale_item->quantity * $use);
                                    } else {
                                        $item_sales[$key] = ($sale_item->quantity * $use);
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
            $period = StockPeriods::findOrFail($last_period);
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
                    $value = $item_price->value;
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
		$summary_invoices = Purchases::where(['stock_period_id' => $last_period])->count();
        $summary_sales = Sales::where(['stock_period_id' => $last_period])->count();
        $summary_menu = Menu::where(['checked' => 0])->count();
        return view('Default.index')->with(array(
            'last_period' => $last_period,
            'date_from' => $period ? date('Y-m-d', strtotime($period->date_from)) : date('Y-m-d', strtotime('-30 days')),
            'date_to' => $period ? date('Y-m-d', strtotime($period->date_to)) : date('Y-m-d', time()),
            'last_stock_summary_items' => $items,
            'variance' => $variance,
            'items' => $items,
            'count' => $count,
            'sales' => count($sales_chart) > 10 ? array_slice($sales_chart, 0, 10) : $sales_chart,
            'wastage' => $wastage,
            'summary' => ['stock' => $summary_stock, 'invoices' => $summary_invoices, 'sales' => $summary_sales, 'menu' => $summary_menu, 'no_price' => $items_without_price]
        ));
	}

    public function exportExcel($stock, $category) {
        $data = $this->getData($stock, $category);
        $c = ItemCategories::findOrFail($category);
        $s = StockPeriods::findOrFail($stock);
        $ready = [];
        $total = 0;
        foreach($data['items'] as $key => $d){
            $ready[$key] = [
                'ID' => $key,
                'Title' => $d['title'],
                'Price per unit (£)' => $d['purchases']['price'] ? $d['purchases']['price'] : 'not set',
                'Opening Stock' => $d['last_stock'].' '.$d['units'],
                'Purchases' => $d['purchases']['value'].' '.$d['units'],
                'Sales' => $d['sales'].' '.$d['units'],
                'Wastage' => $d['wastage'].' '.$d['units'],
                'Predicted' => $d['must_stock'].' '.$d['units'],
                'Closing Stock' => $d['current_stock'].' '.$d['units'],
                'Difference' => $d['stock_difference'].' '.$d['units'],
                'Variance' => '£ '.$d['variance']
            ];
            $total += $d['variance'];
        }
        $ready[] = [
            'ID' => '',
            'Title' => '',
            'Price per unit (£)' => '',
            'Opening Stock' => '',
            'Purchases' => '',
            'Sales' => '',
            'Wastage' => '',
            'Predicted' => '',
            'Closing Stock' => '',
            'Difference' => 'TOTAL',
            'Variance' => '£ '.$total
        ];
        Excel::create('Event', function($excel) use($ready,$s,$c)
        {
            $excel->sheet('Sheetname', function($sheet) use($ready,$s,$c)
            {
                $sheet->setAutoSize(true);
                $sheet->mergeCells('A1:K1');
                $sheet->mergeCells('A2:K2');
                //header
                $sheet->setHeight(1, 40);
                $sheet->row(1, function ($row) {
                    $row->setFontSize(30);
                });
                $sheet->row(1, array('Stock#'.$s->number.' ('.$s->date_from.' - '.($s->date_to ? $s->date_to : 'NOW').')'));
                //category
                $sheet->setHeight(2, 30);
                $sheet->row(2, function ($row) {
                    $row->setFontSize(20);
                });
                $sheet->row(2, array('Category: '.$c->title));
                //table headers
                $sheet->setHeight(3, 20);
                $sheet->row(3, function ($row) {
                    $row->setFontWeight('bold');
                });
                $sheet->cells('C', function($cells) {
                    $cells->setAlignment('left');
                    $cells->setFontSize(10);
                });
                //table data
                $sheet->setFontSize(10);
                $sheet->fromArray($ready, null, 'A3');
            });
        })->export('xls');
    }

    private function getData($stock, $category) {
        $last_period = $stock;
        $current_period = Helper::periodAfterId($last_period);
        $item_purchases = [];
        $last_stock = [];
        $current_stock = [];
        $item_sales = [];
        $item_wastes = [];
        $items = [];
        $wastage = [];
        $sales_chart = [];
        $items_without_price = 0;
        $period = null;
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
                foreach($sale->sales()->orderBy('quantity', 'DESC')->get() as $sale_item){
                    $menu = $sale_item->menu()->first();
                    if($menu){
                        $color = $this->rand_color();
                        $sales_chart[$menu->id] = [
                            'value' => $sale_item->quantity,
                            'color' => $color,
                            'highlight' => $this->alter_brightness($color, 20),
                            'label' => $menu->title
                        ];
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
                                        $item_sales[$key] += ($sale_item->quantity * $use);
                                    } else {
                                        $item_sales[$key] = ($sale_item->quantity * $use);
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
                    $value = $item_price->value;
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
        return $category && array_key_exists($category, $items) ? $items[$category] : $items;
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
