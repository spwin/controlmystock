<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
            <li class="sidebar-search">
                <div class="input-group custom-search-form">
                    <input type="text" class="form-control" placeholder="Search...">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
            </li>
            <li {{ (Request::is('/') ? 'class="active"' : '') }}>
                <a href="{{ url ('') }}"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
            </li>
            <li {{ (Request::is('users/list') ? 'class="active"' : '') }}>
                <a href="{{ action('UsersController@index') }}"><i class="fa fa-user fa-fw"></i> Users</a>
            </li>
            <li {{ (Request::is('units/unit-groups') ? 'class="active"' : '') }}>
                <a href="{{ action('UnitGroupsController@index') }}"><i class="fa fa-folder-open fa-fw"></i> Unit groups</a>
            </li>
            <li {{ (Request::is('units/units') ? 'class="active"' : '') }}>
                <a href="{{ action('UnitsController@index') }}"><i class="fa fa-edit fa-fw"></i> Units</a>
            </li>
            <li {{ (Request::is('items/item-categories') ? 'class="active"' : '') }}>
                <a href="{{ action('ItemCategoriesController@index') }}"><i class="fa fa-folder-open fa-fw"></i> Item categories</a>
            </li>
            <li {{ (Request::is('items/items') ? 'class="active"' : '') }}>
                <a href="{{ action('ItemsController@index') }}"><i class="fa fa-th-list fa-fw"></i> Items</a>
            </li>
            <li {{ (Request::is('stock/stock-check') ? 'class="active"' : '') }}>
                <a href="{{ action('StockCheckController@index') }}"><i class="fa fa-tasks fa-fw"></i> Stock manage</a>
            </li>
            <li {{ (Request::is('recipes/recipes') ? 'class="active"' : '') }}>
                <a href="{{ action('RecipesController@index') }}"><i class="fa fa-file-text-o fa-fw"></i> Recipes</a>
            </li>
        </ul>
    </div>
</div>