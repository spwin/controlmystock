@extends('layouts.dashboard')
@section('page_heading', '"'.$item->title.'" purchases')
@section('section')
    <div class="col-sm-12">
        <a href="{{ action ('SuppliersController@index') }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to Suppliers list</a>
        <div class="row">
            <div class="col-lg-12">
                @section ('table_panel_title', 'Supplier purchases')
                @section ('table_panel_body')
                    <table class="table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Invoice Nr.</th>
                            <th>Price</th>
                            <th>VAT</th>
                            <th>GROSS</th>
                            <th>Created at</th>
                            <th>Delivered at</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($item->purchases()->get() as $purchase)
                            <tr>
                                <td>{{ $purchase->id }}</td>
                                <td><a href="{{ action('ItemPurchasesController@index', $purchase->id) }}">{{ $purchase->number }}</a></td>
                                <td>£ {{ round($purchase->purchases()->sum('price'), 2) }}</td>
                                <td>£ {{ round($purchase->purchases()->sum('vat'), 2) }}</td>
                                <td>£ {{ round($purchase->purchases()->sum('price') + $purchase->purchases()->sum('vat'), 2) }}</td>
                                <td>{{ $purchase->date_created }}</td>
                                <td>{{ $purchase->date_delivered != '0000-00-00' ? $purchase->date_delivered : 'not set' }}</td>
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