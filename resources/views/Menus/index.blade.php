@extends('layouts.dashboard')
@section('page_heading', $title.' list')
@section('section')
    <div class="col-sm-12">
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                {{ Session::get('flash_message') }}
            </div>
        @endif
            <p>
                Menu is generated automatically after Aloha file import.
                @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
                @endif
                {{ Form::open([
                    'method' => 'PATH',
                    'action' => ['MenusController@uploadMenu'],
                    'files' => true
                    ]) }}
                {{ Form::file('file', null, ['class' => 'form-control']) }}
                {{ Form::submit('Import', ['class' => 'btn btn-success btn-xs']) }}
                {{ Form::close() }}
            </p>
        <div class="row">
            <div class="col-lg-8">
                @section ('table_panel_title', $title)
                @section ('table_panel_body')
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Aloha nr.</th>
                                <th>Title</th>
                                <th>Assigned to</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->number }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td>
                                        @if($item->type == 'item')
                                            {{ '<a href="'.action('ItemsController@edit', $item->item()->first()->id).'" class="btn btn-primary btn-xs">'.$item->type.'</a>' }}
                                        @elseif($item->type == 'recipe')
                                            {{ '<a href="'.action('RecipesController@index', $item->recipe()->first()->id).'" class="btn btn-success btn-xs">'.$item->type.'</a>' }}
                                        @else
                                            {{ '<a href="'.action('MenusController@assign', $item->id).'" class="btn btn-danger btn-xs">unassigned</a>' }}
                                        @endif
                                    </td>
                                    <td>£ {{ $item->price }}</td>
                                    <td>
                                        {{ Form::open([
                                        'method' => 'DELETE',
                                        'action' => ['MenusController@destroy', $item->id],
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