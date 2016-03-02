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
            <div class="form-group">
            {{ Form::open([
                    'action' => array('PurchasesController@index'),
                    'class' => ' inline-block pure-form pure-form-aligned',
                    'role' => 'form',
                    'method' => 'get',
                    'id' => 'sales-change-period'
                    ]) }}
                {{ Form::select('stock_period', $stocks_list, $period,  ['id' => 'stock_period', 'class' => 'form-control inline-block', 'style' => 'width: 400px;']) }}
                {{ Form::text('date_from', $date_from, ['id' => 'date_from', 'class' => 'ml-10px form-control inline-block', 'style' => 'width: 160px;']) }} -
                {{ Form::text('date_to', $date_to, ['id' => 'date_to', 'class' => 'form-control inline-block', 'style' => 'width: 160px;']) }}
                @include('widgets.button', array('class'=>'btn btn-warning ml-10px', 'value'=>'Filter by period', 'type' => 'submit'))
            {{ Form::close() }}
            </div>
        <div class="row">
            <div class="col-lg-12">
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
                                <th>NET</th>
                                <th>VAT</th>
                                <th>GROSS</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $total_price = 0;
                                $total_vat = 0;
                            ?>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->number }}</td>
                                    <td>{{ $item->date_created }}</td>
                                    <td>{{ $item->date_delivered }}</td>
                                    <td>{{ $item->supplier()->first() ? '<a href="'.action('SuppliersController@show', $item->supplier()->first()->id).'">'.$item->supplier()->first()->title.'</a>' : '' }}</td>
                                    <td><strong>£ {{ round($item->purchases()->sum('price'), 2) }}</strong></td>
                                    <td><strong>£ {{ round($item->purchases()->sum('vat'), 2) }}</strong></td>
                                    <td><strong>£ {{ round($item->purchases()->sum('price'), 2) + round($item->purchases()->sum('vat'), 2) }}</strong></td>
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
                                    <?php
                                        $total_price += round($item->purchases()->sum('price'), 2);
                                        $total_vat += round($item->purchases()->sum('vat'), 2);
                                    ?>
                                </tr>
                            @endforeach
                            <th colspan="5" style="text-align: right;">TOTAL:</th>
                            <th>£ {{ $total_price }}</th>
                            <th>£ {{ $total_vat }}</th>
                            <th>£ {{ $total_price+$total_vat }}</th>
                            <th></th>
                            <th></th>
                            </tbody>
                        </table>
                @endsection
                @include('widgets.panel', array('header'=>true, 'as'=>'table'))
            </div>
        </div>
    </div>
@stop
@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        var dates = <?php echo json_encode($period_dates); ?>;
        var current = <?php echo $running_period; ?>;
        var now = '<?php echo date('Y-m-d', time()); ?>';
        function onLoad(){
            dates[current].to = now;
            console.log(current);
        }
        function setDates(){
            var period = $('#stock_period').val();
            $('#date_from').val(dates[period].from);
            $('#date_to').val(dates[period].to);
        }
        onLoad();
        $('#stock_period').on('change', function(){
            setDates();
        });
        $( "#date_from" ).datepicker({
            maxDate: 'today',
            dateFormat: 'yy-mm-dd'
        });
        $( "#date_to" ).datepicker({
            maxDate: 'today',
            dateFormat: 'yy-mm-dd'
        });
    });
</script>
@endpush