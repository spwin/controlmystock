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
                {{ Form::open([
                'action' => 'UnitGroupsController@store',
                'class' => 'pure-form pure-form-aligned',
                'role' => 'form',
                'method' => ''
                ]) }}
                    <div class="form-group">
                        {{ Form::label('title', 'Unit group title:', ['class' => 'control-label']) }}
                        {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Title', 'required' => 'required']) }}
                        <p class="help-block">This field must be unique.</p>
                    </div>
                    @include('widgets.button', array('class'=>'btn btn-primary', 'value'=>'Submit', 'type' => 'submit'))
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop