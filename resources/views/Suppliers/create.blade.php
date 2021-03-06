@extends('layouts.dashboard')
@section('page_heading', $title.' create')
@section('section')
    <div class="col-sm-12">
        <a href="{{ action ('SuppliersController@index') }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to list</a>
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
                'action' => 'SuppliersController@store',
                'class' => 'pure-form pure-form-aligned',
                'role' => 'form',
                'method' => ''
                ]) }}
                    <div class="form-group">
                        {{ Form::label('title', 'Supplier name:', ['class' => 'control-label']) }}
                        {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Name', 'required' => 'required']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('vat', 'VAT number:', ['class' => 'control-label']) }}
                        {{ Form::text('vat', null, ['class' => 'form-control', 'placeholder' => 'VAT']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('email', 'Email:', ['class' => 'control-label']) }}
                        {{ Form::input('email', 'email', null, ['class' => 'form-control', 'placeholder' => 'Email']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('phone', 'Phone:', ['class' => 'control-label']) }}
                        {{ Form::text('phone', null, ['class' => 'form-control', 'placeholder' => 'Phone']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('address', 'Address:', ['class' => 'control-label']) }}
                        {{ Form::text('address', null, ['class' => 'form-control', 'placeholder' => 'Address']) }}
                    </div>
                    @include('widgets.button', array('class'=>'btn btn-primary', 'value'=>'Submit', 'type' => 'submit'))
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop