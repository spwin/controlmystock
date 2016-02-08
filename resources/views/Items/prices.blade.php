@extends('layouts.dashboard')
@section('page_heading', $title.' list')
@section('section')
    <div class="col-sm-12">
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                {{ Session::get('flash_message') }}
            </div>
        @endif
        There are only products without any purchase
        @if(count($items) > 0)
        <div class="row">
            <div class="col-lg-6">
                @section ('table_panel_title', $title)
                @section ('table_panel_body')
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Default unit</th>
                                <th>Price</th>
                                <th>Created at</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->category()->first()->title }}</td>
                                    <td>{{ $item->units()->where('default', 1)->first() ? $item->units()->where('default', 1)->first()->unit()->first()->title.' ('.$item->units()->where('default', 1)->first()->unit()->first()->group()->first()->title.')' : '' }} {{ '<a href="'.action('ItemUnitsController@index', $item->id).'" class="btn btn-warning btn-xs">Manage</a>' }}</td>
                                    <td>
                                        @if($item->price)
                                            <strong>£ {{ $item->price }}</strong> per {{ $item->units()->where('default', 1)->first()->unit()->first()->title }}
                                        @else
                                            <span class="btn-danger btn-xs no-hover">NO PRICE!</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>
                                        <a href="{{ action('ItemsController@setPrice', $item->id) }}" class="btn btn-xs btn-success">Set price</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                @endsection
                @include('widgets.panel', array('header'=>true, 'as'=>'table'))
            </div>
        </div>
        @else
            Each product has its price! :)
        @endif
    </div>
@stop