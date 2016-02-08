@extends('layouts.dashboard')
@section('page_heading', $title.' list')
@section('body-tag', 'onload="FocusOnInput(\'q\')"')
@section('section')
    <div class="col-sm-12">
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                {{ Session::get('flash_message') }}
            </div>
        @endif
        <div class="row">
            <div class="col-lg-4">
                {{ Form::open([
                'action' => ['StockCheckController@index'],
                'method' => 'GET',
                'role' => 'form'
                ]) }}
                <div class="form-group input-group">
                    {{ Form::text('q', $search ? $search : '', ['id' =>  'q', 'placeholder' =>  'Item name', 'class' => 'form-control'])}}
                    <span class="input-group-btn"><button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button></span>
                </div>
                {{ Form::close() }}
            </div>
        </div>
        {{ $search ? '<h3>'.(count($items) > 0 ? count($items).' ' : 'no ').'results for "'.$search.'" <a href="'.action('StockCheckController@index').'">(clear)</a></h3>' : '' }}
        @if(count($items) > 0)
        <div class="row">
            <div class="col-lg-10">
                @section ('table_panel_title')
                    @if($filter == 'without_stock')
                        Items with not checked current stock
                        <a href="{{ action('StockCheckController@index') }}" class=" right btn btn-warning btn-xs">Show last 10 updated</a>
                    @else
                        Last 10 updated items stock
                        {{ $not_updated ? '<a href="'.action('StockCheckController@index', ['filter' => 'without_stock']).'" class="right btn btn-warning btn-xs">items to check ('.$not_updated.')</a>' : '' }}
                    @endif
                @endsection
                @section ('table_panel_body')
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Item</th>
                                <th>Last period</th>
                                <th>This period</th>
                                <th>Units</th>
                                <th>Last modified</th>

                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td><a href="{{ action('ItemsController@edit', $item->id) }}">{{ $item->title }}</a></td>
                                    <td>{{ ($item->stock()->where(['stock_period_id' => $previous])->first() ? $item->stock()->where(['stock_period_id' => $previous])->first()->stock : '0').' '.($item->units()->where(['default' => 1])->first() ? $item->units()->where(['default' => 1])->first()->unit()->first()->title : '') }}</td>
                                    <td>{{ ($item->stock()->where(['stock_period_id' => $period])->first() ? $item->stock()->where(['stock_period_id' => $period])->first()->stock : '0').' '.($item->units()->where(['default' => 1])->first() ? $item->units()->where(['default' => 1])->first()->unit()->first()->title : '') }}</td>
                                    <td><a href="{{ action('ItemUnitsController@index', $item->id) }}" class="btn btn-primary btn-xs">Manage</a> {{ $item->units()->where(['default' => 1])->first() ? $item->units()->where(['default' => 1])->first()->unit()->first()->title : '' }}</td>
                                    <td>{{ $item->stock()->orderBy('updated_at', 'DESC')->first() ? $item->stock()->orderBy('updated_at', 'DESC')->first()->updated_at : '' }}</td>
                                    
                                    <td>
                                        <a href="{{ action('StockCheckController@edit', $item->id) }}" class="btn btn-xs btn-success">Edit stock</a>
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
            @if($filter == 'without_stock')
                <h3>All items are up to date!</h3>
                    <a href="{{ action('StockCheckController@index') }}" class="btn-success btn btn-large">Show last 10 updated items</a>
            @else
                <a href="{{ action('ItemsController@create', $search ? ['name' => $search] : []) }}" class="btn-success btn btn-large">Add item</a>
            @endif
        @endif
    </div>
@stop
@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        searchAutocomplete.init($('#q'), '{{ action('StockCheckController@autocomplete') }}');
    });
</script>
@endpush