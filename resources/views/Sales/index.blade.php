@extends('layouts.dashboard')
@section('page_heading', $title.' list')
@section('section')
    <div class="col-sm-12">
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                {{ Session::get('flash_message') }}
            </div>
        @endif
            {{ Form::open([
                    'action' => array('SalesController@index'),
                    'class' => 'pure-form pure-form-aligned',
                    'role' => 'form',
                    'method' => 'get',
                    'id' => 'sales-change-period'
                    ]) }}
            <div class="form-group">
                {{ Form::select('stock_period', $stocks_list, $period,  ['class' => 'form-control inline-block', 'style' => 'width: 400px;']) }}
                @include('widgets.button', array('class'=>'btn btn-warning ml-10px', 'value'=>'Change period', 'type' => 'submit'))
            </div>
            {{ Form::close() }}
            <h3>Upload Aloha file for selected period</h3>
            {{ Form::open([
                    'method' => 'PATH',
                    'action' => ['SalesController@uploadMenu'],
                    'files' => true,
                    'class' => 'mb-20px'
                    ]) }}
            {{ Form::file('file', null, ['class' => 'form-control']) }}
            {{ Form::hidden('stock_period_id', $period) }}
            {{ Form::submit('Import', ['class' => 'btn btn-success btn-xs']) }}
            {{ Form::close() }}
        <div class="row">
            <div class="col-lg-8">
                @section ('table_panel_title', $title)
                @section ('table_panel_body')
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Sale name</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>Sale imported on {{ $item->created_at }}</td>
                                    <td>£ {{ round($item->sales()->sum('total_price'), 2) }}</td>
                                    <td>
                                        <a href="{{ action('SaleItemsController@index', ['id' => $item->id, 'stock_period' => $period]) }}" class="btn btn-xs btn-primary">Show items</a>
                                        {{ Form::open([
                                        'method' => 'DELETE',
                                        'action' => ['SalesController@destroy', $item->id],
                                        'class' => 'inline-block',
                                        'onclick'=> 'return confirm("Are you sure?")'
                                        ]) }}
                                        {{ Form::hidden('stock_period_id', $period) }}
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