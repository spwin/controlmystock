@foreach ($categories as $category)
    <li>
        @if ($category->children()->first())
            <span><i class="fa fa-fw fa-minus-circle"></i> {{ $category->title }}</span>
            <a href="{{ action('ItemCategoriesController@create', array('parent' => $category->id)) }}" class="btn btn-xs btn-success"><i class="fa fa-fw fa-plus"></i></a>
            <a href="{{ action('ItemCategoriesController@edit', $category->id) }}" class="btn btn-xs btn-warning"><i class="fa fa-fw fa-pencil"></i></a>
            {{ Form::open([
            'method' => 'DELETE',
            'action' => ['ItemCategoriesController@destroy', $category->id],
            'class' => 'inline-block'
            ]) }}
            {{ Form::button('<i class="fa fa-fw fa-trash-o"></i>', array(
            'type' => 'submit',
            'class'=> 'btn btn-xs btn-danger',
            'onclick'=>'return confirm("Are you sure?")'
            )); }}
            {{ Form::close() }}
            <ul>
                @include('ItemCategories.tree', array('categories' => $category->children()->get()))
            </ul>
        @else
            <span><i class="fa fa-fw fa-asterisk"></i> {{ $category->title }}</span>
            <a href="{{ action('ItemCategoriesController@create', array('parent' => $category->id)) }}" class="btn btn-xs btn-success"><i class="fa fa-fw fa-plus"></i></a>
            <a href="{{ action('ItemCategoriesController@edit', $category->id) }}" class="btn btn-xs btn-warning"><i class="fa fa-fw fa-pencil"></i></a>
            {{ Form::open([
            'method' => 'DELETE',
            'action' => ['ItemCategoriesController@destroy', $category->id],
            'class' => 'inline-block'
            ]) }}
            {{ Form::button('<i class="fa fa-fw fa-trash-o"></i>', array(
            'type' => 'submit',
            'class'=> 'btn btn-xs btn-danger',
            'onclick'=>'return confirm("Are you sure?")'
            )); }}
            {{ Form::close() }}
        @endif
    </li>
@endforeach