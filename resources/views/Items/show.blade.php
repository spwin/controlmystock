@extends('layouts.dashboard')
@section('page_heading', $title.' list')
@section('body-tag', 'onload="FocusOnInput(\'q\')"')
@section('section')
    <div class="col-sm-12">
        <a href="{{ action ('ItemsController@index') }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to Items list</a>
        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Recipes</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Usage</th>
                                <th>Created at</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($item->recipes()->get() as $recipe)
                                <tr>
                                    <td>{{ $recipe->recipe()->first()->id }}</td>
                                    <td><a href="{{ action('RecipeItemsController@index', $recipe->recipe()->first()->id) }}">{{ $recipe->recipe()->first()->title }}</a></td>
                                    <td>{{ $recipe->value }} {{ $item->units()->where(['default' => 1])->first()->unit()->first()->title }}</td>
                                    <td>{{ $recipe->recipe()->first()->created_at }}</td>
                                    <td>
                                        <a href="{{ action('RecipeItemsController@index', $recipe->recipe()->first()->id) }}" class="btn btn-xs btn-primary">Show</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Menu</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Usage</th>
                                <th>Created at</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($item->menus()->get() as $menu)
                                <tr>
                                    <td>{{ $menu->id }}</td>
                                    <td><a href="{{ action('MenusController@assign', $menu->id) }}">{{ $menu->title }}</a></td>
                                    <td>{{ $menu->value }} {{ $item->units()->where(['default' => 1])->first()->unit()->first()->title }}</td>
                                    <td>{{ $menu->created_at }}</td>
                                    <td>
                                        <a href="{{ action('MenusController@assign', $menu->id) }}" class="btn btn-xs btn-primary">Show</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop