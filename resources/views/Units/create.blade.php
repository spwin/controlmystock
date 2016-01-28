@extends('layouts.dashboard')
@section('page_heading', $title.' create')
@section('body-tag', 'onload="FocusOnInput(\'title\')"')
@section('section')
    <div class="col-sm-12">
        <a href="{{ action ('UnitsController@index') }}" class="mb-20px block"><i class="fa fa-arrow-left fa-fw"></i>Back to list</a>
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
                'action' => 'UnitsController@store',
                'class' => 'pure-form pure-form-aligned',
                'role' => 'form',
                'method' => '',
                'id' => 'unit-create-form'
                ]) }}
                <div class="form-group">
                    {{ Form::label('title', 'Unit title:', ['class' => 'control-label']) }}
                    {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Title', 'required' => 'required']) }}
                    <p class="help-block">This field must be unique.</p>
                </div>

                <div class="form-group">
                    {{ Form::label('group_id', 'Unit group:', ['class' => 'control-label']) }}
                    {{ Form::select('group_id', $groups, null, ['class' => 'form-control', 'required' => 'required']) }}
                </div>

                <div class="form-group factor-select">
                    <div class="w-40p inline-block" style="width: 40%;">
                        {{ Form::label('factor', 'Factor:', ['class' => 'control-label']) }}
                        {{ Form::input('number', 'factor', null, ['class' => 'form-control', 'placeholder' => 'Units', 'required' => 'required']) }}
                    </div>
                    <div class="w-10p inline-block" style="widht: 10%">
                        <span id="current-unit-js"></span> =
                    </div>
                    <div class="w-40p inline-block" style="widht: 40%">
                        {{ Form::input('number', null, 1, ['class' => 'form-control', 'placeholder' => 'Default', 'required' => 'required', 'id' => 'factor_default']) }}
                    </div>
                    <div class="w-10p inline-block" style="widht: 10%">
                        <span id="default-unit-js">ml</span>
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        {{ Form::checkbox('default', null); }} Make this unit default
                    </label>
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
        var defaults = <?php echo json_encode($defaults); ?>;
        var select = $('select[name="group_id"]');
        function checkDefaultUnit(value){
            $('#default-unit-js').html(defaults[value]);
        }
        checkDefaultUnit(select.val());
        $(select).on('change', function(){
            checkDefaultUnit($(this).val());
        });

        $('input#title').on('change', function(){
            $('#current-unit-js').html($(this).val());
        });

        $('form#unit-create-form').on('submit', function(e){
            var input = $('input#factor');
            var factor = input.val();
            var factor_default = $('input#factor_default').val();
            if(factor && factor_default && parseFloat(factor_default) != 0){
                input.val(parseFloat(factor)/parseFloat(factor_default));
            } else {
                input.val(1);
            }
            return true;
        });
    });
    function FocusOnInput(name)
    {
        document.getElementById(name).focus();
    }
</script>
@endpush