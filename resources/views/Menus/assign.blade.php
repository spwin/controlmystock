@extends('layouts.dashboard')
@section('page_heading', $title.' assign')
@section('section')
    <div class="col-sm-12">
        <a href="{{ action ('MenusController@index') }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to list</a>
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <div class="row">
            <div class="col-lg-6">
                {{ Form::model($item, [
                'method' => 'PATCH',
                'action' => ['MenusController@update', $item->id],
                'id' => 'menu-assign-form'
                ]) }}

                <div class="form-group">
                    {{ Form::label('number', 'Menu item nr.:', ['class' => 'control-label']) }}
                    {{ Form::text('number', null, ['class' => 'form-control', 'required' => 'required', 'disabled' => 'disabled']) }}
                </div>

                <div class="form-group">
                    {{ Form::label('title', 'Menu item title:', ['class' => 'control-label']) }}
                    {{ Form::text('title', null, ['class' => 'form-control', 'required' => 'required', 'disabled' => 'disabled']) }}
                </div>

                <div class="form-group">
                    {{ Form::label('price', 'Price:', ['class' => 'control-label']) }}
                    {{ Form::text('price', null, ['class' => 'form-control', 'required' => 'required', 'disabled' => 'disabled']) }}
                </div>

                <div class="form-group">
                    <label>Assign with:</label>
                    <div class="radio">
                        <label>
                            {{ Form::radio('type', 'item') }} Item
                        </label>
                        <label>
                            {{ Form::radio('type', 'recipe') }} Recipe
                        </label>
                        <label>
                            {{ Form::radio('type', 'none') }} Unassign
                        </label>
                    </div>
                </div>

                <div class="changed-section-form item_form">
                    <div class="form-group">
                        {{ Form::label('item_id', 'Select Item to assign with:', ['class' => 'control-label']) }}
                        {{ Form::select('item_id', $items, null, ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group">
                        <div class="w-40p inline-block" style="width: 40%;">
                            {{ Form::label('value', 'Product usage in menu:', ['class' => 'control-label']) }}
                            {{ Form::input('number', 'value', null, ['step' => 'any', 'class' => 'form-control']) }}
                        </div>
                        <div class="inline-block">
                            {{ Form::select('units', $items_units['php_list'][key($items)], null, ['id' => 'units', 'class' => 'form-control', 'required' => 'required']) }}
                        </div>
                    </div>
                </div>

                <div class="changed-section-form recipe_form">
                    <div class="form-group">
                        {{ Form::label('recipe_id', 'Select Recipe to assign with:', ['class' => 'control-label']) }}
                        {{ Form::select('recipe_id', $recipes, null, ['class' => 'form-control']) }}
                    </div>
                </div>

                {{ Form::hidden('checked', 1) }}
                {{ Form::submit('Assign '.$title, ['class' => 'btn btn-primary']) }}

                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop
@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        MenuItemForm.init('{{ $item->type }}', $('.item_form'), $('.recipe_form'), $('.changed-section-form'), $('input[name="type"]'), $('#menu-assign-form'), $('select#item_id'), $('select#units'), $('input#value'), <?php echo json_encode($items_units['list']); ?>, <?php echo json_encode($items_units['factors']); ?>);
    });
</script>
@endpush