@extends('layouts.dashboard')
@section('page_heading', '"'.$item->title.'" usage in database')
@section('section')
    <div class="col-sm-12">
        <a href="{{ action ('ItemsController@index') }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to Items list</a>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Recipes</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Usage</th>
                                <th>Created at</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($item->recipes()->get() as $recipe)
                                <tr>
                                    <td>{{ $recipe->recipe()->first()->id }}</td>
                                    <td><a href="{{ action('RecipeItemsController@index', $recipe->recipe()->first()->id) }}">{{ $recipe->recipe()->first()->title }}</a></td>
                                    <td>{{ $recipe->value }} {{ $item->units()->where(['default' => 1])->first()->unit()->first()->title }}</td>
                                    <td>{{ $recipe->recipe()->first()->created_at }}</td>
                                    <td>
                                        <a href="{{ action('RecipeItemsController@index', $recipe->recipe()->first()->id) }}" class="btn btn-xs btn-primary">Show</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Menu</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Usage</th>
                                <th>Created at</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($item->menus()->where(['type' => 'item'])->get() as $menu)
                                <tr>
                                    <td>{{ $menu->id }}</td>
                                    <td><a href="{{ action('MenusController@assign', $menu->id) }}">{{ $menu->title }}</a></td>
                                    <td>{{ $menu->value }} {{ $item->units()->where(['default' => 1])->first()->unit()->first()->title }}</td>
                                    <td>{{ $menu->created_at }}</td>
                                    <td>
                                        <a href="{{ action('MenusController@assign', $menu->id) }}" class="btn btn-xs btn-primary">Show</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Item purchases</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Invoice Nr.</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>VAT</th>
                                <th>Created at</th>
                                <th>Delivered at</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($item->purchases()->get() as $purchase)
                                <tr>
                                    <td>{{ $purchase->id }}</td>
                                    <td><a href="{{ action('ItemPurchasesController@index', $purchase->purchase()->first()->id) }}">{{ $purchase->purchase()->first()->number }}</a></td>
                                    <td>{{ $purchase->value }} {{ $purchase->item()->first()->units()->where(['default' => 1])->first()->unit()->first()->title }}</td>
                                    <td>£ {{ $purchase->price }}</td>
                                    <td>{{ $purchase->vat ? '£ '.$purchase->vat : 'no VAT' }}</td>
                                    <td>{{ $purchase->purchase()->first()->date_created }}</td>
                                    <td>{{ $purchase->purchase()->first()->date_delivered != '0000-00-00' ? $purchase->purchase()->first()->date_delivered : 'not set' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        @section ('table_panel_title', 'Stock changes')
                        @section ('table_panel_body')
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Route</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($history as $h)
                                    <tr>
                                        <td>{{ $h->created_at }}</td>
                                        <td>{{ str_replace('App\Http\Controllers\\', '', $h->action) }}</td>
                                        <td><strong>{{ $h->username }}</strong> {{ $h->message }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endsection
                        @include('widgets.panel', array('header'=>true, 'as'=>'table'))
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop