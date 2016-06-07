@extends('layouts.dashboard')
@section('page_heading', 'Add item to invoice')
@section('body-tag', 'onload="FocusOnInput(\'item_custom\')"')
@section('section')
    <div class="col-sm-12">
        <a href="{{ action ('ItemPurchasesController@index', $purchase->id) }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to list</a>
        <h2>Invoice Nr.{{ $purchase->number }}</h2>
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <div class="row">
            <div class="col-lg-4">
                {{ Form::open([
                'action' => 'ItemPurchasesController@store',
                'class' => 'pure-form pure-form-aligned',
                'role' => 'form',
                'method' => '',
                'id' => 'invoice-add-item-form'
                ]) }}
                {{ Form::hidden('purchase_id', $purchase->id, ['readonly' => 'readonly', 'required' => 'required']) }}
                {{ Form::hidden('type', $type, ['readonly' => 'readonly', 'required' => 'required']) }}

                <div class="form-group">
                    {{ Form::label('item_custom', 'Item name:', ['class' => 'control-label']) }}
                    {{ Form::text('item_custom', null, ['class' => 'form-control', 'required' => 'required']) }}
                </div>

                <div class="form-group">
                    <div class="w-40p inline-block" style="width: 40%;">
                        {{ Form::label('value_entered', 'Product quantity:', ['class' => 'control-label']) }}
                        {{ Form::input('number', 'value_entered', null, ['step' => 'any', 'class' => 'form-control', 'required' => 'required']) }}
                    </div>
                    <div class="w-10p inline-block" style="width: 20%;">
                        {{ Form::label('unit_custom', 'Unit:', ['class' => 'control-label']) }}
                        {{ Form::text('unit_custom', null, ['step' => 'any', 'class' => 'form-control', 'required' => 'required']) }}
                    </div>
                </div>

                <div class="form-group">
                    <div class="w-40p inline-block" style="width: 40%;">
                        {{ Form::label('price', 'Total price:', ['class' => 'control-label']) }}
                        {{ Form::input('number', 'price', null, ['step' => 'any', 'class' => 'form-control', 'required' => 'required']) }}
                    </div>
                    <div class="inline-block">
                        <input type="checkbox" id="vat_checkbox" style="margin: 0 10px;">
                    </div>
                    <div class="w-30p inline-block" style="width: 30%;">
                        {{ Form::label('vat', 'VAT:', ['class' => 'control-label']) }}
                        {{ Form::input('number', 'vat', null, ['step' => 'any', 'class' => 'form-control', 'disabled' => 'disabled']) }}
                    </div>
                    <p class="help-block"><a href="#" class="generate-price-fom-total">Generate from total</a></p>
                </div>

                <div class="form-group generator-price" style="display: none;">
                    <div class="w-40p inline-block" style="width: 40%;">
                        {{ Form::label('price_vat', 'Total + VAT:', ['class' => 'control-label']) }}
                        {{ Form::input('number', 'price_vat', null, ['step' => 'any', 'class' => 'form-control']) }}
                    </div>
                    <div class="inline-block">
                        <a class="btn btn-warning generate-price">Generate price</a>
                    </div>
                </div>

                @include('widgets.button', array('class'=>'btn btn-primary', 'value'=>'Submit', 'type' => 'submit'))
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop
@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $('#vat_checkbox').on('click', function(e){
            var vat = $('#vat');
            var price = $('#price');
            if(price.val()) {
                if ($(this).is(':checked')) {
                    vat.prop('disabled', false);
                    vat.val((price.val()*0.2).toFixed(2));
                } else {
                    vat.prop('disabled', true);
                    vat.val('');
                }
            } else {
                e.preventDefault();
                price.focus();
            }
        });
        $('.generate-price-fom-total').click(function(){
            $('.generator-price').slideToggle('fast');
        });
        $('.generate-price').click(function(){
            var total = parseFloat($('input#price_vat').val());
            var price = total/1.2;
            var vat = price*0.2;
            $('input#vat_checkbox').prop('checked', true);
            $('input#vat').prop('disabled', false);
            $('input#price').val(price.toFixed(2));
            $('input#vat').val(vat.toFixed(2));
            $('.generator-price').hide();
        });
    });
</script>
@endpush