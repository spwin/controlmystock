@extends('layouts.dashboard')
@section('page_heading', $title.' list')
@section('section')
    <div class="col-sm-12">
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                {{ Session::get('flash_message') }}
            </div>
        @endif
        <a href="{{ action ('PurchaseCategoriesController@create') }}" class="mb-20px block"><i class="fa fa-plus-circle fa-fw"></i>Add {{ $title }}</a>
        <div class="row">
            <div class="col-lg-12">
                @section ('table_panel_title', $title)
                @section ('table_panel_body')
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $category->id }}</td>
                                    <td>{{ $category->title }}</td>
                                    <td>{{ $category->created_at }}</td>
                                    <td>
                                        <a href="{{ action('PurchaseCategoriesController@edit', $category->id) }}" class="btn btn-xs btn-success">Edit</a>
                                        {{ Form::open([
                                        'method' => 'DELETE',
                                        'action' => ['PurchaseCategoriesController@destroy', $category->id],
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
    </div>
@stop