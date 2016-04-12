@extends('layouts.dashboard')
@section('page_heading', $title)
@section('section')
    <div class="col-sm-12">
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                {{ Session::get('flash_message') }}
            </div>
        @endif
        <a class="btn btn-success inline-block mb-20px" href="{{ action('ItemsController@exportPricesExcel') }}">Export excel</a>
        <div class="row">
            <div class="col-lg-12">
                @section ('table_panel_title', $title)
                @section ('table_panel_body')
                        <table class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Category</th>
                                <th>Item name</th>
                                <th>Unit</th>
                                <th>Price per unit</th>
                                <th>Supplier</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($items as $id => $item)
                                <tr>
                                    <td>{{ $id }}</td>
                                    <td>{{ $item['category'] }}</td>
                                    <td>{{ $item['title'] }}</td>
                                    <td>{{ $item['unit'] }}</td>
                                    <td>£ {{ $item['price'] }}</td>
                                    <td>{{ $item['supplier'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                @endsection
                @include('widgets.panel', array('header'=>true, 'as'=>'table'))
            </div>
        </div>
    </div>
@stop