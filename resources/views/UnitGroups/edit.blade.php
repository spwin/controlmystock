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
                {{ Form::model($UnitGroup, [
                'method' => 'PATCH',
                'action' => ['UnitGroupsController@update', $UnitGroup->id]
                ]) }}

                <div class="form-group">
                    {{ Form::label('title', 'Unit group title:', ['class' => 'control-label']) }}
                    {{ Form::text('title', null, ['class' => 'form-control', 'required' => 'required']) }}
                </div>

                {{ Form::submit('Update '.$title, ['class' => 'btn btn-primary']) }}

                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop