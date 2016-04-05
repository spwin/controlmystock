@extends('layouts.dashboard')
@section('page_heading', $title.' create')
@section('section')
    <div class="col-sm-12">
        <a href="{{ action ('PurchasesController@index') }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to list</a>
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <div class="row">
            <div class="col-lg-6">
                {{ Form::open([
                'action' => 'PurchasesController@store',
                'class' => 'pure-form pure-form-aligned',
                'role' => 'form',
                'method' => '',
                'files' => true
                ]) }}
                    <div class="form-group">
                        {{ Form::label('file', 'Invoice file:', ['class' => 'control-label']) }}
                        {{ Form::file('file', null, ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('number', 'Invoice number:', ['class' => 'control-label']) }}
                        {{ Form::text('number', null, ['class' => 'form-control', 'placeholder' => 'VAT', 'required' => 'required']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('category_id', 'Purchase category:', ['class' => 'control-label']) }}
                        {{ Form::select('category_id', [''] + $categories, null, ['class' => 'form-control', 'required' => 'required']) }}
                        <p class="help-block">Not found suitable category? <a href="{{ action('PurchaseCategoriesController@create') }}">Create new category</a></p>
                    </div>
                    <div class="form-group">
                        {{ Form::label('vat_date', 'VAT data:', ['class' => 'control-label']) }}
                        {{ Form::text('vat_date', null, ['class' => 'form-control', 'placeholder' => 'VAT date']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('date_created', 'Date created:', ['class' => 'control-label']) }}
                        {{ Form::text('date_created', null, ['class' => 'form-control', 'placeholder' => 'Created']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('date_delivered', 'Date delivered:', ['class' => 'control-label']) }}
                        {{ Form::text('date_delivered', null, ['class' => 'form-control', 'placeholder' => 'Delivered']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('supplier_id', 'Supplier:', ['class' => 'control-label']) }}
                        {{ Form::text('custom_supplier', null, ['class' => 'form-control', 'placeholder' => 'Supplier name', 'style' => 'display: none;']) }}
                        {{ Form::select('supplier_id', $suppliers, null, ['class' => 'form-control']) }}
                        {{ Form::checkbox('new_supplier', null); }} New supplier
                    </div>
                    <div class="form-group">
                        {{ Form::label('stock_period_id', 'Stock period:', ['class' => 'control-label']) }}
                        {{ Form::select('stock_period_id', $stocks_list, $period, ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group">
                        <label>
                            {{ Form::checkbox('status', null); }} Mark as paid
                        </label>
                    </div>
                    @include('widgets.button', array('class'=>'btn btn-primary', 'value'=>'Submit', 'type' => 'submit'))
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop
@push('scripts')
<script type="text/javascript">
    function setCustomer(){
        if($('input[name="new_supplier"]').is(':checked')){
            $('#supplier_id').hide();
            $('input[name="custom_supplier"]').show();
        } else {
            $('#supplier_id').show();
            $('input[name="custom_supplier"]').hide();
        }
    }
    $(document).ready(function(){
        setCustomer();
    });
    $('input[name="new_supplier"]').on('click', function(){
        $('#supplier_id').toggle();
        $('input[name="custom_supplier"]').toggle();
    });
    $(function() {
        $( "#date_created" ).datepicker({
            maxDate: 'today',
            dateFormat: 'yy-mm-dd'
        });
        $( "#date_delivered" ).datepicker({
            maxDate: 'today',
            dateFormat: 'yy-mm-dd'
        });
        $( "#vat_date" ).datepicker({
            maxDate: 'today',
            dateFormat: 'yy-mm-dd'
        });
    });
</script>
@endpush