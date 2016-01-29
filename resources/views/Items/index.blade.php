@extends('layouts.dashboard')
@section('page_heading', $title.' list')
@section('body-tag', 'onload="FocusOnInput(\'q\')"')
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
                    'action' => ['ItemsController@index'],
                    'method' => 'GET',
                    'role' => 'form'
                    ]) }}
                    <div class="form-group input-group">
                        {{ Form::text('q', $search ? $search : '', ['id' =>  'q', 'placeholder' =>  'Item name', 'class' => 'form-control'])}}
                        <span class="input-group-btn"><button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button></span>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
            <a href="{{ action ('ItemsController@create') }}" class="mb-20px block"><i class="fa fa-plus-circle fa-fw"></i>Add {{ $title }}</a>
        {{ $search ? '<h3>'.(count($items) > 0 ? count($items).' ' : 'no ').'results for "'.$search.'" <a href="'.action('ItemsController@index').'">(clear)</a></h3>' : '' }}
        @if(count($items) > 0)
        <div class="row">
            <div class="col-lg-6">
                @section ('table_panel_title', $title)
                @section ('table_panel_body')
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Default unit</th>
                                <th>Created at</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->category()->first()->title }}</td>
                                    <td>{{ $item->units()->where('default', 1)->first() ? $item->units()->where('default', 1)->first()->unit()->first()->title.' ('.$item->units()->where('default', 1)->first()->unit()->first()->group()->first()->title.')' : '' }} {{ '<a href="'.action('ItemUnitsController@index', $item->id).'" class="btn btn-warning btn-xs">Manage</a>' }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>
                                        <a href="{{ action('ItemsController@edit', $item->id) }}" class="btn btn-xs btn-success">Edit</a>
                                        {{ Form::open([
                                        'method' => 'DELETE',
                                        'action' => ['ItemsController@destroy', $item->id],
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
        @endif
    </div>
@stop
@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        searchAutocomplete.init($('#q'), '{{ action('StockCheckController@autocomplete') }}');
    });
</script>
@endpush