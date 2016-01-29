@extends('layouts.dashboard')
@section('page_heading', $title.' list')
@section('section')
    <div class="col-sm-12">
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                {{ Session::get('flash_message') }}
            </div>
        @endif
        <a href="{{ action ('ItemCategoriesController@create') }}" class="mb-20px block"><i class="fa fa-plus-circle fa-fw"></i>Add {{ $title }}</a>
        <div class="row">
            <div class="col-sm-6">
                <div class="tree well">
                    <ul>
                        <li>
                            <span><i class="fa fa-fw fa-folder-open"></i> Categories</span>
                            <a href="{{ action('ItemCategoriesController@create') }}" class="btn btn-xs btn-success"><i class="fa fa-fw fa-plus"></i></a>
                            @if(count($root_categories) > 0)
                                <ul>
                                    @include('ItemCategories.tree', array('categories' => $root_categories))
                                </ul>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
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
                            <th>Parent</th>
                            <th>Created</th>
                            <th>Items count</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->title }}</td>
                                <td>{{ $category->parent()->first() ? $category->parent()->first()->title : '-' }}</td>
                                <td>{{ $category->created_at }}</td>
                                <td>{{ $category->items->count() }}</td>
                                <td>
                                    <a href="{{ action('ItemCategoriesController@edit', $category->id) }}" class="btn btn-xs btn-success">Edit</a>
                                    {{ Form::open([
                                    'method' => 'DELETE',
                                    'action' => ['ItemCategoriesController@destroy', $category->id],
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

@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        drawCategoriesTree.init('.tree');
    });
</script>
@stop