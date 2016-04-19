<div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="{{ url ('') }}">Stock control v1.0</a>
</div>
<ul class="nav navbar-top-links navbar-right">
    <?php $period = \Helper::getDefaultPeriod() ?>
    {{ 'Default stock: <strong>Stock N#'.$period->number.' ('.$period->date_from.' - '.($period->date_to ? $period->date_to : 'NOW').')</strong>' }}

    <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
            {{ isset(Auth::user()->name) ? Auth::user()->name : Auth::user()->email }}
            <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
        </a>
        <ul class="dropdown-menu dropdown-user">
            <li><a href="{{ action ('Auth\AuthController@getLogout') }}"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
            </li>
        </ul>
    </li>
</ul>