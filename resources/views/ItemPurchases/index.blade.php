@extends('layouts.dashboard')
@section('page_heading', $title.' list')
@section('section')
    <div class="col-sm-12">
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                {{ Session::get('flash_message') }}
            </div>
        @endif
            <a href="{{ action ('PurchasesController@index') }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to purchases list</a>
            <h2><strong>Invoice Nr.{{ $purchase->number }}</strong> items</h2>
            <div class="mb-20px block">
                <a href="{{ action('ItemPurchasesController@create', ['item_id' => $purchase->id, 'type' => 'item']) }}" class="btn btn-primary btn-large"><i class="fa-plus-circle fa fa-fw"></i>Add item from list</a>
                <a href="{{ action('ItemPurchasesController@create', ['item_id' => $purchase->id, 'type' => 'custom']) }}" class="btn btn-primary btn-large ml-10px"><i class="fa-plus-circle fa fa-fw"></i>Add custom item</a>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    @section ('table_panel_title', $title)
                    @section ('table_panel_body')
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Per unit</th>
                                <th>Price</th>
                                <th>VAT</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $total_price = 0; $total_vat = 0; ?>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->type == 'item' ? $item->item()->first()->title : $item->item_custom }}</td>
                                    <td>{{ $item->value }}</td>
                                    <td>{{ $item->type == 'item' ? $item->item()->first()->units()->where(['default' => 1])->first()->unit()->first()->title : $item->unit_custom }}</td>
                                    <td>{{ $item->value == 0 ? '-' : '£ '.round($item->price/$item->value, 2) }}</td>
                                    <td>£ {{ $item->price }}</td>
                                    <td>{{ $item->vat ? '£ '.$item->vat : 'no VAT' }}</td>
                                    <td>
                                        {{ '<a href="'.action('ItemPurchasesController@edit', $item->id).'" class="btn btn-warning btn-xs">Edit</a>' }}
                                        {{ Form::open([
                                        'method' => 'DELETE',
                                        'action' => ['ItemPurchasesController@destroy', $item->id],
                                        'class' => 'inline-block',
                                        'onclick'=>'return confirm("Are you sure?")'
                                        ]) }}
                                        {{ Form::submit('Remove', ['class' => 'btn btn-danger btn-xs']) }}
                                        {{ Form::close() }}
                                    </td>
                                </tr>
                                <?php $total_price += $item->price; $total_vat += $item->vat; ?>
                            @endforeach
                            {{ Form::open([
                            'method' => '',
                            'action' => ['ItemPurchasesController@generate', $purchase->id]
                            ]) }}
                                <th colspan="5" style="text-align: right;">TOTAL:</th>
                                <th><input name="total_price" type="text" value="{{ $total_price }}" style="width: 80px; position: absolute;"></th>
                                <th><input name="total_vat" type="text" value="{{ $total_vat }}" style="width: 80px; position: absolute;"></th>
                            <th>{{ Form::submit('Generate', ['class' => 'btn btn-success btn-xs']) }}</th>
                            {{ Form::close() }}
                            </tbody>
                        </table>
                    @endsection
                    @include('widgets.panel', array('header'=>true, 'as'=>'table'))
                </div>
            </div>
    </div>
@stop