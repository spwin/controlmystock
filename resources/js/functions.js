function FocusOnInput(name) {
    document.getElementById(name).focus();
}

var drawCategoriesTree = function () {
    var name;
    return {
        init: function(tree){
            name = tree;
            drawCategoriesTree.collapse();
            drawCategoriesTree.bind();
        },
        collapse: function(){
            $(name+' li:has(ul)').addClass('parent_li').find(' > span').attr('title', 'Hide');
        },
        bind: function(){
            $(name+' li.parent_li > span').on('click', function (e) {
                var children = $(this).parent('li.parent_li').find(' > ul > li');
                if (children.is(":visible")) {
                    children.hide('fast');
                    $(this).attr('title', 'Show').find(' > i').addClass('fa-plus-circle').removeClass('fa-minus-circle');
                } else {
                    children.show('fast');
                    $(this).attr('title', 'Hide').find(' > i').addClass('fa-minus-circle').removeClass('fa-plus-circle');
                }
                e.stopPropagation();
            });
        }
    };
}();

var searchAutocomplete = function(){
    var search;
    var source;
    return {
        init: function(query, url){
            search = query;
            source = url;
            searchAutocomplete.getData();
        },
        getData: function(){
            search.autocomplete({
                source: source,
                delay: 0
            });
        }
    }
}();

var itemUnitsForm = function(){
    var unit_groups_list;
    var unit_factors_list;
    var select;
    var default_set;
    return {
        init: function(ugl, ufl, s, ds){
            unit_groups_list = ugl;
            unit_factors_list = ufl;
            select = s;
            default_set = ds;
            itemUnitsForm.checkCurrentUnit();
            itemUnitsForm.checkIfShowFactor();
            itemUnitsForm.bind();
        },
        checkCurrentUnit: function(){
            $('#current-unit-js').html(select.find(":selected").text());
        },
        checkIfShowFactor: function(){
            if(unit_groups_list[select.val()] == default_set){
                $('.factor-select').find('input').attr('readonly', 'readonly');
                var input = $('input#factor');
                var factor_default = $('input#factor_default').val();
                input.val(unit_factors_list[select.val()]);
            } else {
                $('input#factor').val('');
                $('.factor-select').find('input').attr('readonly', false);
            }
        },
        bind: function(){
            $(select).on('change', function(){
                itemUnitsForm.checkCurrentUnit();
                itemUnitsForm.checkIfShowFactor();
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
        }
    }
}();

var recipeItemForm = function(){
    var form;
    var select;
    var units_select;
    var value;
    var items_units;
    var units;
    return {
        init: function(f, s, us, v, iu, u){
            form = f;
            select = s;
            units_select = us;
            value = v;
            items_units = iu;
            units = u;
            recipeItemForm.bind();
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
        }
    }
}();

var stockForm = function(){
    var form;
    return {
        init: function(f){
            form = f;
            stockForm.bind();
        },
        bind: function(){
            form.on('submit', function(){
                var count = parseFloat($('#count').val());
                var quantity = parseFloat($('#quantity').val());
                count = (isNaN(count) ? 1 : count);
                $('input[name="value"]').val(count * quantity);
                return true;
            });
        }
    }
}();

var unitsForm = function(){
    var defaults;
    var select;
    return {
        init: function(d, s, prepare){
            defaults = d;
            select = s;
            unitsForm.checkDefaultUnit();
            unitsForm.bind();
            if(prepare){
                unitsForm.prepare();
            }
        },
        prepare: function(){
            $('#current-unit-js').html($('input#title').val());
            unitsForm.checkDefaultUnit(select.val());
        },
        checkDefaultUnit: function(value){
            $('#default-unit-js').html(defaults[value]);
        },
        bind: function(){
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
            $(select).on('change', function(){
                unitsForm.checkDefaultUnit($(this).val());
            });
            $('input#title').on('change', function(){
                $('#current-unit-js').html($(this).val());
            });
        }
    }
}();

var purchaseItemForm = function(){
    var form;
    var select;
    var units_select;
    var value;
    var items_units;
    var units;
    var entered_value;
    var item_to_unit;
    return {
        init: function(f, s, us, v, ve, iu, u, itu){
            form = f;
            select = s;
            units_select = us;
            value = v;
            entered_value = ve;
            items_units = iu;
            units = u;
            item_to_unit = itu;
            purchaseItemForm.bind();
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
                value.val(entered_value.val()*units[units_select.val()]);
                units_select.val(item_to_unit[units_select.val()]);
                return true;
            });
        }
    }
}();

var MenuItemForm = function(){
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
    return {
        init: function(it, i_f, rf, csf, t, f, s, us, v, iu, u){
            item_type = it;
            item_form = i_f;
            recipe_form = rf;
            section = csf;
            type = t;
            form = f;
            select = s;
            units_select = us;
            value = v;
            items_units = iu;
            units = u;
            MenuItemForm.bind();
            MenuItemForm.changeSection(item_type);
        },
        changeSection: function(name){
            section.hide();
            if(name == 'item'){
                item_form.show();
            } else if(name == 'recipe'){
                recipe_form.show();
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
                MenuItemForm.changeSection($(this).val());
            });
        }
    }
}();
