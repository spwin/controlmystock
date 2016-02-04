@extends('layouts.dashboard')
@section('page_heading', $title.' create')
@section('section')
    <div class="col-sm-12">
        <a href="{{ action ('UnitGroupsController@index') }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to list</a>
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <div class="row">
            <div class="col-lg-6">
                {{ Form::model($Suppliers, [
                'method' => 'PATCH',
                'action' => ['SuppliersController@update', $Suppliers->id]
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

                {{ Form::submit('Update '.$title, ['class' => 'btn btn-primary']) }}

                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop