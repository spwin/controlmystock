@extends('layouts.dashboard')
@section('page_heading', $title.' list')
@section('section')
    <div class="col-sm-12">
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                {{ Session::get('flash_message') }}
            </div>
        @endif
        <a href="{{ action ('WasteReasonsController@create') }}" class="mb-20px block"><i class="fa fa-plus-circle fa-fw"></i>Add {{ $title }}</a>
        <div class="row">
            <div class="col-lg-8">
                @section ('table_panel_title', $title)
                @section ('table_panel_body')
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Reason</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($reasons as $reason)
                                <tr>
                                    <td>{{ $reason->id }}</td>
                                    <td>{{ $reason->reason }}</td>
                                    <td>{{ $reason->created_at }}</td>
                                    <td>
                                        <a href="{{ action('WasteReasonsController@edit', $reason->id) }}" class="btn btn-xs btn-success">Edit</a>
                                        {{ Form::open([
                                        'method' => 'DELETE',
                                        'action' => ['WasteReasonsController@destroy', $reason->id],
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