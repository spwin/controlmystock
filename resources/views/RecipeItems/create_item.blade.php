@extends('layouts.dashboard')
@section('page_heading', 'Add item for recipe <strong>'.$recipe->title.'</strong>')
@section('section')
    <div class="col-sm-12">
        <a href="{{ action ('RecipeItemsController@index', ['recipe_id' => $recipe->id]) }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to list</a>
        <h2>{{ $recipe->title }}</h2>
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <div class="row">
            <div class="col-lg-4">
                {{ Form::open([
                'action' => 'RecipeItemsController@store',
                'class' => 'pure-form pure-form-aligned',
                'role' => 'form',
                'method' => '',
                'id' => 'recipe-add-item-form'
                ]) }}
                {{ Form::hidden('recipe_id', $recipe->id, ['readonly' => 'readonly', 'required' => 'required']) }}
                {{ Form::hidden('type', $type, ['readonly' => 'readonly', 'required' => 'required']) }}

                <div class="form-group">
                    {{ Form::label('item_id', 'Select product:', ['class' => 'control-label']) }}
                    {{ Form::select('item_id', $items, null, ['class' => 'form-control', 'required' => 'required']) }}
                    <p class="help-block">Can't find a product? <a href="{{ action('ItemsController@create') }}">Create new product</a></p>
                </div>

                <div class="form-group">
                    <div class="w-40p inline-block" style="width: 40%;">
                        {{ Form::label('value', 'Product usage in recipe:', ['class' => 'control-label']) }}
                        {{ Form::input('number', 'value', null, ['step' => 'any', 'class' => 'form-control', 'required' => 'required']) }}
                    </div>
                    <div class="w-10p inline-block" style="widht: 10%">
                        {{ Form::select('units', $items_units['php_list'][key($items)], null, ['id' => 'units', 'class' => 'form-control', 'required' => 'required']) }}
                    </div>
                </div>

                @include('widgets.button', array('class'=>'btn btn-primary', 'value'=>'Submit', 'type' => 'submit'))
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop
@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        var form = $('#recipe-add-item-form');
        var select = $('select#item_id');
        var units_select = $('select#units');
        var value = $('input#value');
        var items_units = <?php echo json_encode($items_units['list']); ?>;
        var units = <?php echo json_encode($items_units['factors']); ?>;

        select.on('change', function(){
            units_select.html('');
            var select_population = items_units[$(this).val()];
            for(key in select_population){
                units_select.append('<option value="'+select_population[key]['id']+'">'+select_population[key]['title']+'</option>');
            }
        });

        form.on('submit', function(){
            value.val(value.val()*units[units_select.val()]);
            return true;
        });
    });
</script>
@endpush