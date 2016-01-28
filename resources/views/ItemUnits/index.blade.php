@extends('layouts.dashboard')
@section('page_heading', $title.' list')
@section('section')
    <div class="col-sm-12">
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                {{ Session::get('flash_message') }}
            </div>
        @endif
        <a href="{{ action ('ItemsController@index') }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to items list</a>
        <h2><strong>{{ $item->title }}</strong> units</h2>
        <div class="mb-20px block">
            <a href="{{ action('ItemUnitsController@create', $item->id) }}" class="btn btn-primary btn-large"><i class="fa-plus-circle fa fa-fw"></i>Add</a>
        </div>
        <div class="row">
            <div class="col-sm-6">
                @section ('table_panel_title', $title)
                @section ('table_panel_body')
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Default</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Factor</th>
                                <th>Created at</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->default ? '<button type="button" class="btn btn-success btn-xs">default</button>' : '<a href="'.action ('ItemUnitsController@setDefault', ['id' => $item->id]) .'">set default</a>' }}</td>
                                    <td>{{ $item->unit()->first()->title }}</td>
                                    <td>{{ $item->unit()->first()->group()->first()->title }}</td>
                                    <td>{{ $default_unit ? '1 '.$item->unit()->first()->title.' = '.$item->factor.' '.$default_unit->unit()->first()->title : '' }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>
                                        {{ Form::open([
                                        'method' => 'DELETE',
                                        'action' => ['ItemUnitsController@destroy', $item->id],
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