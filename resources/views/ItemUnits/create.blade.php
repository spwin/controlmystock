@extends('layouts.dashboard')
@section('page_heading', 'Create unit for <strong>'.$item->title.'</strong>')
@section('section')
    <div class="col-sm-12">
        <a href="{{ action ('ItemUnitsController@index', ['item_id' => $item->id]) }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to list</a>
        <h2>{{ $item->title }}</h2>
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
                'action' => 'ItemUnitsController@store',
                'class' => 'pure-form pure-form-aligned',
                'role' => 'form',
                'method' => '',
                'id' => 'unit-create-form'
                ]) }}
                {{ Form::hidden('item_id', $item->id, ['readonly' => 'readonly', 'required' => 'required']) }}

                <div class="form-group">
                    {{ Form::label('unit_id', 'Select unit:', ['class' => 'control-label']) }}
                    {{ Form::select('unit_id', $units, null, ['class' => 'form-control', 'required' => 'required']) }}
                    <p class="help-block">Can't find suitable unit? <a href="{{ action('UnitsController@create') }}">Create new unit</a></p>
                </div>
                @if($default_unit)
                    <div class="form-group">
                        <label>
                            {{ Form::checkbox('default', null); }} Make this unit default for <strong>{{ $item->title }}</strong>
                        </label>
                    </div>

                    <div class="form-group factor-select">
                        <div class="w-40p inline-block" style="width: 40%;">
                            {{ Form::label('factor', 'Factor:', ['class' => 'control-label']) }}
                            {{ Form::input('number', 'factor', null, ['class' => 'form-control', 'placeholder' => 'Units']) }}
                        </div>
                        <div class="w-10p inline-block" style="widht: 10%">
                            <span id="current-unit-js"></span> =
                        </div>
                        <div class="w-40p inline-block" style="widht: 40%">
                            {{ Form::input('number', null, 1, ['class' => 'form-control', 'placeholder' => 'Default', 'id' => 'factor_default']) }}
                        </div>
                        <div class="w-10p inline-block" style="widht: 10%">
                            <span id="default-unit-js">{{ $default_unit->unit()->first()->title }}</span>
                        </div>
                    </div>
                @else
                    {{ Form::hidden('default', 1); }}
                    {{ Form::hidden('factor', 1); }}
                @endif
                @include('widgets.button', array('class'=>'btn btn-primary', 'value'=>'Submit', 'type' => 'submit'))
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop

@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        var unit_groups_list = <?php echo json_encode($unit_groups_list); ?>;
        var unit_factors_list = <?php echo json_encode($unit_factors_list); ?>;
        var select = $('select[name="unit_id"]');
        var default_set = <?php echo $default_unit ? $default_unit->unit()->first()->group()->first()->id : 0 ?>;

        function checkIfShowFactor(){
            if(unit_groups_list[select.val()] == default_set){
                $('.factor-select').find('input').attr('readonly', 'readonly');
                var input = $('input#factor');
                var factor_default = $('input#factor_default').val();
                input.val(unit_factors_list[select.val()]);
            } else {
                $('input#factor').val('');
                $('.factor-select').find('input').attr('readonly', false);
            }
        }

        function checkCurrentUnit(){
            $('#current-unit-js').html(select.find(":selected").text());
        }

        checkCurrentUnit();
        checkIfShowFactor();

        $(select).on('change', function(){
            checkCurrentUnit();
            checkIfShowFactor();
        });

        $('form#unit-create-form').on('submit', function(e){
            var input = $('input#factor');
            var factor = input.val();
            var factor_default = $('input#factor_default').val();
            if(factor && factor_default && parseFloat(factor_default) != 0){
                input.val(parseFloat(factor_default)/parseFloat(factor));
            } else {
                input.val(1);
            }
            return true;
        });
    });
</script>
@endpush