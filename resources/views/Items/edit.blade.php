@extends('layouts.dashboard')
@section('page_heading', $title.' create')
@section('section')
    <div class="col-sm-12">
        <a href="{{ action ('ItemsController@index') }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to list</a>
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <div class="row">
            <div class="col-lg-4">
                {{ Form::model($Items, [
                'method' => 'PATCH',
                'action' => ['ItemsController@update', $Items->id],
                'id' => 'item-create-form',
                'class' => 'pure-form pure-form-aligned',
                'role' => 'form'
                ]) }}
                <div class="form-group">
                    {{ Form::label('title', 'Item title:', ['class' => 'control-label']) }}
                    {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Title', 'required' => 'required']) }}
                    <p class="help-block">This field must be unique.</p>
                </div>

                <div class="form-group">
                    {{ Form::label('category_id', 'Item category:', ['class' => 'control-label']) }}
                    {{ Form::select('category_id', $categories, null, ['class' => 'form-control', 'required' => 'required']) }}
                </div>
                @include('widgets.button', array('class'=>'btn btn-primary', 'value'=>'Submit', 'type' => 'submit'))
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop