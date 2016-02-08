@extends('layouts.dashboard')
@section('page_heading', $title.' list')
@section('section')
    <div class="col-sm-12">
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                {{ Session::get('flash_message') }}
            </div>
        @endif
            <div class="row">
                <div class="col-lg-6">
                    {{ Form::open([
                    'action' => ['RecipesController@index'],
                    'method' => 'GET',
                    'role' => 'form'
                    ]) }}
                    <div class="form-group input-group">
                        {{ Form::text('q', $search ? $search : '', ['id' =>  'q', 'placeholder' =>  'Recipe name', 'class' => 'form-control'])}}
                        <span class="input-group-btn"><button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button></span>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        <a href="{{ action ('RecipesController@create') }}" class="mb-20px block"><i class="fa fa-plus-circle fa-fw"></i>Add {{ $title }}</a>
        {{ $search ? '<h3>'.(count($recipes) > 0 ? count($recipes).' ' : 'no ').'results for "'.$search.'" <a href="'.action('RecipesController@index').'">(clear)</a></h3>' : '' }}
        @if(count($recipes) > 0)
        <div class="row">
            <div class="col-lg-8">
                @section ('table_panel_title', $title)
                @section ('table_panel_body')
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Recipe name</th>
                                <th>Created</th>
                                <th>Items</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($recipes as $recipe)
                                <tr>
                                    <td>{{ $recipe->id }}</td>
                                    <td><a href="{{ action('RecipesController@show', $recipe->id) }}">{{ $recipe->title }}</a></td>
                                    <td>{{ $recipe->created_at }}</td>
                                    <td><a href="{{ action('RecipeItemsController@index', $recipe->id) }}" class="btn btn-primary btn-xs">Manage items</a></td>
                                    <td>
                                        <a href="{{ action('RecipesController@edit', $recipe->id) }}" class="btn btn-xs btn-success">Edit</a>
                                        {{ Form::open([
                                        'method' => 'DELETE',
                                        'action' => ['RecipesController@destroy', $recipe->id],
                                        'class' => 'inline-block',
                                        'onclick'=> 'return confirm("Are you sure?")'
                                        ]) }}
                                        {{ Form::submit('Delete', ['class' => 'btn btn-danger btn-xs']) }}
                                        {{ Form::close() }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                @endsection
                @include('widgets.panel', array('header'=>true, 'as'=>'table'))
            </div>
        </div>
        @else
            <a href="{{ action('RecipesController@create') }}" class="btn-success btn btn-large">Add recipe</a>
        @endif
    </div>
@stop