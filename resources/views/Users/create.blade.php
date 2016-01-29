@extends('layouts.dashboard')
@section('page_heading', $title.' create')
@section('section')
    <div class="col-sm-12">
        <a href="{{ action ('UsersController@index') }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to list</a>
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
                'action' => 'UsersController@store',
                'class' => 'pure-form pure-form-aligned',
                'role' => 'form',
                'method' => ''
                ]) }}
                    <div class="form-group">
                        {{ Form::label('name', 'User name:', ['class' => 'control-label']) }}
                        {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Username', 'required' => 'required']) }}
                        <p class="help-block">This field must be unique.</p>
                    </div>

                    <div class="form-group">
                        {{ Form::label('email', 'E-mail:', ['class' => 'control-label']) }}
                        {{ Form::input('email', 'email', null, ['class' => 'form-control', 'placeholder' => 'Email', 'required' => 'required']) }}
                    </div>

                    <div class="form-group">
                        {{ Form::label('password', 'Password:', ['class' => 'control-label']) }}
                        {{ Form::input('password', 'password', null, ['class' => 'form-control', 'placeholder' => '', 'required' => 'required']) }}
                    </div>

                    <div class="form-group">
                        {{ Form::label('role', 'Select role:', ['class' => 'control-label']) }}
                        {{ Form::select('role', ['admin' => 'admin', 'user' => 'user','client' => 'client'], null, ['class' => 'form-control', 'placeholder' => 'Title', 'required' => 'required']) }}
                    </div>

                    <div class="form-group">
                        <label>
                            {{ Form::checkbox('active', null); }} Set this user active
                        </label>
                    </div>
                    @include('widgets.button', array('class'=>'btn btn-primary', 'value'=>'Submit', 'type' => 'submit'))
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop