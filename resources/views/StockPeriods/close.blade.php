@extends('layouts.dashboard')
@section('page_heading', $title.' close')
@section('section')
    <div class="col-sm-12">
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                {{ Session::get('flash_message') }}
            </div>
        @endif
        <a href="{{ action ('StockPeriodsController@index') }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to list</a>
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
                'action' => 'StockPeriodsController@store',
                'class' => 'pure-form pure-form-aligned',
                'role' => 'form',
                'method' => ''
                ]) }}
                {{ Form::hidden('last_period', $last_period) }}
                    <div class="form-group">
                        {{ Form::label('date_from', 'Period close date:', ['class' => 'control-label']) }}
                        {{ Form::text('date_from', date('Y-m-d H:i:s', time()), ['class' => 'form-control', 'placeholder' => 'Pick date','readonly' => 'readonly', 'required' => 'required']) }}
                        <p class="help-block">This field must be unique.</p>
                    </div>
                    @include('widgets.button', array('class'=>'btn btn-primary', 'value'=>'Submit', 'type' => 'submit'))
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop