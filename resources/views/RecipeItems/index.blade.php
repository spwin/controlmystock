@extends('layouts.dashboard')
@section('page_heading', $title.' list')
@section('section')
    <div class="col-sm-12">
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                {{ Session::get('flash_message') }}
            </div>
        @endif
        <a href="{{ action ('RecipesController@index') }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to recipes list</a>
        <h2><strong>{{ $recipe->title }}</strong> - recipe items</h2>
        <div class="mb-20px block">
            <a href="{{ action('RecipeItemsController@create', ['recipe_id' => $recipe->id, 'type' => 'item']) }}" class="btn btn-primary btn-large"><i class="fa-plus-circle fa fa-fw"></i>Add item</a>
            <a href="{{ action('RecipeItemsController@create', ['recipe_id' => $recipe->id, 'type' => 'recipe']) }}" class="btn btn-primary btn-large ml-10px"><i class="fa-plus-circle fa fa-fw"></i>Add recipe</a>
        </div>
        <div class="row">
            <div class="col-sm-6">
                @section ('table_panel_title', $title)
                @section ('table_panel_body')
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Usage</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->type == 'recipe' ? $item->subrecipe()->first()->title : $item->item()->first()->title }}</td>
                                    <td>{{ $item->value }} {{ $item->item()->first() ? $item->item()->first()->units()->where(['default' => 1])->first()->unit()->first()->title : ' of this' }}</td>
                                    <td>{{ $item->type == 'item' ? '<a href="'.action('ItemsController@edit', $item->item()->first()->id).'" class="btn btn-primary btn-xs">show '.$item->type.'</a>' : '<a href="'.action('RecipeItemsController@index', $item->subrecipe()->first()->id).'" class="btn btn-warning btn-xs">show '.$item->type.'</a>' }}</td>
                                    <td>
                                        {{ Form::open([
                                        'method' => 'DELETE',
                                        'action' => ['RecipeItemsController@destroy', $item->id],
                                        'class' => 'inline-block',
                                        'onclick'=>'return confirm("Are you sure?")'
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
    </div>
@stop