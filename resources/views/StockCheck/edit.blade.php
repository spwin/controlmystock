@extends('layouts.dashboard')
@section('page_heading', $title.' for <strong>'.$item->title.'</strong>')
@section('body-tag', 'onload="FocusOnInput(\'quantity\')"')
@section('section')
    <div class="col-sm-12">
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                {{ Session::get('flash_message') }}
            </div>
        @endif
        <a href="{{ action ('StockCheckController@index') }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to list</a>
        <div class="row">
            <div class="col-lg-10">
                @if($default)
                    <div class="panel panel-green" style="max-width: 600px;">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-5">
                                    <i class="fa fa-tasks fa-5x"></i><div class="huge inline-block ml-10px" style="position: absolute;">Stock:</div>
                                </div>
                                <div class="col-xs-7 text-right">
                                    <div class="huge">{{ ($item->stock()->where(['stock_period_id' => $period])->first() ? $item->stock()->where(['stock_period_id' => $period])->first()->stock : '0').' '.$default->unit()->first()->title }}</div>
                                    @if($other)
                                        <?php $other_array = []; ?>
                                        @foreach($item->units()->where(['default' => 0])->get() as $unit)
                                            <?php $other_array[] = (($item->stock()->where(['stock_period_id' => $period])->first() ? $item->stock()->where(['stock_period_id' => $period])->first()->stock : '0') * ($default->factor / $unit->factor)) . ' ' .$unit->unit()->first()->title ?>
                                        @endforeach
                                        {{ '( '.implode(', ', $other_array).' )' }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{ Form::open([
                    'action' => 'StockCheckController@store',
                    'class' => 'pure-form pure-form-aligned',
                    'role' => 'form',
                    'method' => '',
                    'id' => 'stock-check-create-form'
                    ]) }}
                    <div class="form-group">
                        {{ Form::select('action', $actions, null,  ['class' => 'form-control inline-block', 'required' => 'required', 'style' => 'width: 130px;']) }}
                        {{ Form::input('number', 'count', 1,  ['id' => 'count', 'min' => '0', 'class' => 'form-control inline-block ml-10px', 'style' => 'width: 100px;']) }}
                        x
                        {{ Form::input('number', 'quantity', '',  ['id' => 'quantity', 'min' => '0', 'class' => 'form-control inline-block', 'required' => 'required', 'style' => 'width: 100px;']) }}
                        {{ Form::select('unit_id', [$default->id => $default->unit()->first()->title] + $other_units, null,  ['class' => 'ml-10px form-control inline-block', 'required' => 'required', 'style' => 'width: 130px;']) }}
                        @include('widgets.button', array('class'=>'btn btn-warning ml-10px', 'value'=>'Proceed', 'type' => 'submit'))
                    </div>
                    {{ Form::hidden('value', null) }}
                    {{ Form::hidden('item_id', $item->id) }}
                    {{ Form::close() }}
                @else
                    <h2>{{ $item->title }}</h2>
                    <h5>Item has no units, you must first create an unit for this item</h5>
                    <a href="{{ action('ItemUnitsController@index', $item->id) }}" class="btn btn-large btn-success">Add units</a>
                @endif
            </div>
        </div>
        @if($default)
        <div class="row">
            <div class="col-lg-6">
                @section ('table_panel_title', 'Last 10 stock changes')
                @section ('table_panel_body')
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Action</th>
                            <th>Stock before</th>
                            <th>Stock after</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($stock as $event)
                            <tr>
                                <td>{{ $event->created_at }}</td>
                                <td>{{ $event->action == 'add' ? 'Added' : ($event->action == 'reduce' ? 'Reduced by' : 'Changed to') }} {{ $event->value.' '.$event->unit()->first()->title }}</td>
                                <td>{{ $event->before.' '.$item->units()->where(['default' => 1])->first()->unit()->first()->title }}</td>
                                <td>{{ $event->after.' '.$item->units()->where(['default' => 1])->first()->unit()->first()->title }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endsection
                @include('widgets.panel', array('header'=>true, 'as'=>'table'))
            </div>
        </div>
        @endif
    </div>
@stop

@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        stockForm.init($('#stock-check-create-form'));
    });
</script>
@endpush
