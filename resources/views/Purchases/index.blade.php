@extends('layouts.dashboard')
@section('page_heading', $title.' list')
@section('section')
    <div class="col-sm-12">
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                {{ Session::get('flash_message') }}
            </div>
        @endif
        <a href="{{ action ('PurchasesController@create') }}" class="mb-20px block"><i class="fa fa-plus-circle fa-fw"></i>Add {{ $title }}</a>
        <div class="row">
            <div class="col-lg-8">
                @section ('table_panel_title', $title)
                @section ('table_panel_body')
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Invoice Nr</th>
                                <th>Created</th>
                                <th>Delivered</th>
                                <th>Supplier</th>
                                <th>Total</th>
                                <th>VAT</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->number }}</td>
                                    <td>{{ $item->date_created }}</td>
                                    <td>{{ $item->date_delivered }}</td>
                                    <td>{{ $item->supplier()->first() ? $item->supplier()->first()->title : '' }}</td>
                                    <td><strong>£ {{ round($item->purchases()->sum('price'), 2) }}</strong></td>
                                    <td><strong>£ {{ round($item->purchases()->sum('vat'), 2) }}</strong></td>
                                    <td>{{ $item->status ? '<span class="btn-success btn-xs no-hover">Paid</span>' : '<span class="btn-warning btn-xs no-hover">Pending</span>' }}</td>
                                    <td>
                                        <a href="{{ action('ItemPurchasesController@index', $item->id) }}" class="btn btn-xs btn-primary">Manage Items</a>
                                        <a href="{{ action('PurchasesController@edit', $item->id) }}" class="btn btn-xs btn-success">Edit</a>
                                        {{ Form::open([
                                        'method' => 'DELETE',
                                        'action' => ['PurchasesController@destroy', $item->id],
                                        'class' => 'inline-block',
                                        'onclick'=> 'return confirm("Are you sure?")'
                                        ]) }}
                                        {{ Form::submit('Delete', ['class' => 'btn btn-danger btn-xs']) }}
                                        {{ Form::close() }}
                                    </td>
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