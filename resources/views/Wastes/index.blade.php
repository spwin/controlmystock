@extends('layouts.dashboard')
@section('page_heading', $title.' list')
@section('section')
    <div class="col-sm-12">
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                {{ Session::get('flash_message') }}
            </div>
        @endif
            {{ Form::open([
                    'action' => array('WastesController@index'),
                    'class' => 'pure-form pure-form-aligned',
                    'role' => 'form',
                    'method' => 'get',
                    'id' => 'wastes-change-period'
                    ]) }}
            <div class="form-group">
                {{ Form::select('stock_period', $stocks_list, $period,  ['class' => 'form-control inline-block', 'style' => 'width: 400px;']) }}
                @include('widgets.button', array('class'=>'btn btn-warning ml-10px', 'value'=>'Change period', 'type' => 'submit'))
            </div>
            {{ Form::close() }}
            <a href="{{ action ('WastesController@create', ['stock_period' => $period]) }}" class="mb-20px block"><i class="fa fa-plus-circle fa-fw"></i>Add {{ $title }} for selected period</a>
        <div class="row">
            <div class="col-lg-8">
                @section ('table_panel_title', $title)
                @section ('table_panel_body')
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Title</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>
                                        @if($item->type == 'item')
                                            {{ '<a href="'.action('ItemUnitsController@index', $item->item()->first()->id).'" class="btn btn-primary btn-xs">'.$item->type.'</a>' }}
                                        @elseif($item->type == 'recipe')
                                            {{ '<a href="'.action('RecipeItemsController@index', $item->recipe()->first()->id).'" class="btn btn-success btn-xs">'.$item->type.'</a>' }}
                                        @elseif($item->type == 'menu')
                                            {{ '<a href="'.action('MenusController@assign', $item->menu()->first()->id).'" class="btn btn-warning btn-xs">'.$item->type.'</a>' }}
                                        @else
                                            {{ '<a href="" class="btn btn-danger btn-xs">unassigned</a>' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->type == 'item')
                                            {{ $item->value.' '.$item->item()->first()->units()->where(['default' => 1])->first()->unit()->first()->title.' of '.$item->item()->first()->title }}
                                        @elseif($item->type == 'recipe')
                                            {{ $item->recipe()->first()->title }}
                                        @elseif($item->type == 'menu')
                                            {{ $item->menu()->first()->title }}
                                        @endif
                                    </td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>
                                        {{ $item->type ? '<a href="'.action('WastesController@edit', $item->id).'" class="btn btn-warning btn-xs">Edit</a>' : '' }}
                                        {{ Form::open([
                                        'method' => 'DELETE',
                                        'action' => ['WastesController@destroy', $item->id],
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