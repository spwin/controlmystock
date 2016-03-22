@extends('layouts.dashboard')
@section('page_heading', $title.' create')
@section('section')
    <div class="col-sm-12">
        <a href="{{ action ('PurchaseCategoriesController@index') }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to list</a>
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
                'action' => 'PurchaseCategoriesController@store',
                'class' => 'pure-form pure-form-aligned',
                'role' => 'form',
                'method' => ''
                ]) }}
                    <div class="form-group">
                        {{ Form::label('title', 'Category title:', ['class' => 'control-label']) }}
                        {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Title', 'required' => 'required']) }}
                    </div>
                    @include('widgets.button', array('class'=>'btn btn-primary', 'value'=>'Submit', 'type' => 'submit'))
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop