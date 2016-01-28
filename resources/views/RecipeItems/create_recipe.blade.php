@extends('layouts.dashboard')
@section('page_heading', 'Include recipe for <strong>'.$recipe->title.'</strong>')
@section('section')
    <div class="col-sm-12">
        <a href="{{ action ('RecipeItemsController@index', ['recipe_id' => $recipe->id]) }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to list</a>
        <h2>{{ $recipe->title }}</h2>
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <div class="row">
            <div class="col-lg-4">
                {{ Form::open([
                'action' => 'RecipeItemsController@store',
                'class' => 'pure-form pure-form-aligned',
                'role' => 'form',
                'method' => '',
                'id' => 'recipe-add-recipe-form'
                ]) }}
                {{ Form::hidden('recipe_id', $recipe->id, ['readonly' => 'readonly', 'required' => 'required']) }}
                {{ Form::hidden('type', $type, ['readonly' => 'readonly', 'required' => 'required']) }}

                <div class="form-group">
                    {{ Form::label('sub_recipe', 'Select a recipe to include:', ['class' => 'control-label']) }}
                    {{ Form::select('sub_recipe', $recipes, null, ['class' => 'form-control', 'required' => 'required']) }}
                    <p class="help-block">Can't find a recipe? <a href="{{ action('RecipesController@create') }}">Create a recipe</a></p>
                </div>

                <div class="form-group">
                    {{ Form::label('value', 'How much of that do you need:', ['class' => 'control-label']) }}
                    <div class="w-40p" style="width: 40%;">
                        {{ Form::input('number', 'value', null, ['step' => 'any', 'class' => 'form-control', 'required' => 'required']) }}
                    </div>
                </div>

                @include('widgets.button', array('class'=>'btn btn-primary', 'value'=>'Submit', 'type' => 'submit'))
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop