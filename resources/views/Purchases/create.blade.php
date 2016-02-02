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
                        {{ Form::label('date_created', 'Date created:', ['class' => 'control-label']) }}
                        {{ Form::text('date_created', null, ['class' => 'form-control', 'placeholder' => 'Created']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('date_delivered', 'Date delivered:', ['class' => 'control-label']) }}
                        {{ Form::text('date_delivered', null, ['class' => 'form-control', 'placeholder' => 'Delivered']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('supplier_id', 'Supplier:', ['class' => 'control-label']) }}
                        {{ Form::select('supplier_id', $suppliers, null, ['class' => 'form-control']) }}
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
    $(function() {
        $( "#date_created" ).datepicker({
            maxDate: 'today',
            dateFormat: 'yy-mm-dd'
        });
        $( "#date_delivered" ).datepicker({
            maxDate: 'today',
            dateFormat: 'yy-mm-dd'
        });
    });
</script>
@endpush