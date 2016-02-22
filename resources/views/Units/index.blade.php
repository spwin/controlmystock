@extends('layouts.dashboard')
@section('page_heading', $title.' list')
@section('section')
    <div class="col-sm-12">
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                {{ Session::get('flash_message') }}
            </div>
        @endif
        <a href="{{ action ('UnitsController@create') }}" class="mb-20px block"><i class="fa fa-plus-circle fa-fw"></i>Add {{ $title }}</a>
        <div class="row">
            <div class="col-lg-12">
                @section ('table_panel_title', $title)
                @section ('table_panel_body')
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Group</th>
                                <th>Default</th>
                                <th>Factor</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->group()->first()->title }}</td>
                                    <td>{{ $item->default ? '<button type="button" class="btn btn-primary btn-xs">Default</button>' : '' }}</td>
                                    <td>{{ $item->factor != 0 ? 1/$item->factor : 0 }} {{ isset($defaults[$item->group()->first()->id])?$defaults[$item->group()->first()->id]:'' }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>
                                        <a href="{{ action('UnitsController@edit', $item->id) }}" class="btn btn-xs btn-success">Edit</a>
                                        {{ Form::open([
                                        'method' => 'DELETE',
                                        'action' => ['UnitsController@destroy', $item->id],
                                        'class' => 'inline-block',
                                        'onclick'=> $item->disable_delete ? '' : 'return confirm("Are you sure?")'
                                        ]) }}
                                        {{ Form::submit('Delete', ['class' => 'btn btn-danger btn-xs', $item->disable_delete ? 'disabled' : '' => '']) }}
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