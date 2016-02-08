@extends('layouts.dashboard')
@section('page_heading', $title.' "'.$item->title.'"')
@section('section')
    <div class="col-sm-12">
        <a href="{{ action ('ItemsController@prices') }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to list</a>
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <div class="row">
            <div class="col-lg-4">
                {{ Form::model($item, [
                'method' => '',
                'action' => ['ItemsController@updatePrice', $item->id],
                'id' => 'item-price-form',
                'class' => 'pure-form pure-form-aligned',
                'role' => 'form'
                ]) }}
                <div class="form-group">
                    {{ Form::label('price', 'Item price (per '.$item->units()->where(['default' => 1])->first()->unit()->first()->title.'):', ['class' => 'control-label']) }}
                    {{ Form::input('number', 'price', null, ['class' => 'form-control', 'step' => 'any']) }}
                </div>

                @include('widgets.button', array('class'=>'btn btn-primary', 'value'=>'Submit', 'type' => 'submit'))
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop