@extends('layouts.dashboard')
@section('page_heading', $title.' assign')
@section('section')
    <div class="col-sm-12">
        <a href="{{ action ('WastesController@index') }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to list</a>
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <div class="row">
            <div class="col-lg-6">
                {{ Form::model($waste, [
                'method' => 'PATCH',
                'action' => ['WastesController@update', $waste->id],
                'id' => 'wastes-form'
                ]) }}

                <div class="form-group">
                    {{ Form::label('stock_period_id', 'Stock period:', ['class' => 'control-label']) }}
                    {{ Form::select('stock_period_id', $stocks_list, $waste->stock_period_id, ['class' => 'form-control']) }}
                </div>

                <div class="form-group">
                    {{ Form::label('reason_id', 'Select waste reason:', ['class' => 'control-label']) }}
                    {{ Form::select('reason_id', $reasons, null, ['class' => 'form-control']) }}
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
                            {{ Form::radio('type', 'menu') }} Menu
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

                <div class="changed-section-form menu_form">
                    <div class="form-group">
                        {{ Form::label('menu_id', 'Select Recipe to assign with:', ['class' => 'control-label']) }}
                        {{ Form::select('menu_id', $menus, null, ['class' => 'form-control']) }}
                    </div>
                </div>

                {{ Form::submit('Assign '.$title, ['class' => 'btn btn-primary']) }}

                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop
@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        var WasteForm = function(){
            var form;
            var select;
            var units_select;
            var value;
            var items_units;
            var units;
            var type;
            var section;
            var item_form;
            var recipe_form;
            var item_type;
            var menu_form;
            return {
                init: function(it, i_f, rf, mf, csf, t, f, s, us, v, iu, u){
                    item_type = it;
                    item_form = i_f;
                    recipe_form = rf;
                    menu_form = mf;
                    section = csf;
                    type = t;
                    form = f;
                    select = s;
                    units_select = us;
                    value = v;
                    items_units = iu;
                    units = u;
                    WasteForm.bind();
                    WasteForm.changeSection(item_type);
                },
                changeSection: function(name){
                    section.hide();
                    if(name == 'item'){
                        item_form.show();
                    } else if(name == 'recipe'){
                        recipe_form.show();
                    } else if(name == 'menu'){
                        menu_form.show();
                    }
                },
                bind: function(){
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

                    type.on('change', function(){
                        WasteForm.changeSection($(this).val());
                    });
                }
            }
        }();
        WasteForm.init('{{ $waste->type }}', $('.item_form'), $('.recipe_form'), $('.menu_form'), $('.changed-section-form'), $('input[name="type"]'), $('#wastes-form'), $('select#item_id'), $('select#units'), $('input#value'), <?php echo json_encode($items_units['list']); ?>, <?php echo json_encode($items_units['factors']); ?>);
    });
</script>
@endpush