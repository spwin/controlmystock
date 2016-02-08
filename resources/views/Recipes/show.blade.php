@extends('layouts.dashboard')
@section('page_heading', '"'.$item->title.'" usage in database')
@section('section')
    <div class="col-sm-12">
        <a href="{{ action ('RecipesController@index') }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to Items list</a>
        <div class="row">
            <div class="col-lg-8">
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
                                <th>Created at</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($item->menus()->where(['type' => 'recipe'])->get() as $menu)
                                <tr>
                                    <td>{{ $menu->id }}</td>
                                    <td><a href="{{ action('MenusController@assign', $menu->id) }}">{{ $menu->title }}</a></td>
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