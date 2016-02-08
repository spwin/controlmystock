@extends('layouts.dashboard')
@section('page_heading','Last period info')
@section('section')
<!-- /.row -->
<style>
    a.summary-header{
        font-size: 26px;
    }
    a.summary-header:focus{
        color: #337ab7;
        text-decoration: none;
    }
    a.summary-header:hover{
        color: #23527c;
        text-decoration: underline;
    }
    ul.summary{
        list-style: none;
    }
    ul.summary li.header{
        font-size: 24px;
    }
    ul.summary .table-container{
        font-size: 16px;
        display: none;
    }
</style>
<div class="col-sm-12" style="margin-bottom: 50px;">
    <div class="row">
        <div class="col-lg-10">
            <h2>VARIANCE: <strong {{ $variance >= 0 ? 'class="text-success"' : 'class="text-danger"' }}>£ {{ $variance }}</strong></h2>
            <a href="" class="summary-header" id="show_all_variances"><i class="fa-arrow-circle-down fa fa-fw"></i>Show all ({{ $count }})</a>
            <ul class="summary">
            @foreach ($items as $key => $item)
                <li class="header"><a href="" class="summary-header category-summary"><i class="fa-arrow-circle-down fa fa-fw"></i> {{ $item['category'] }} ({{ count($item['items']).' items' }}) variance: <span {{ $item['variance'] >= 0 ? 'class="text-success"' : 'class="text-danger"' }}>£ {{ $item['variance'] }}</span></a>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Price (avg)</th>
                                <th>Opening</th>
                                <th>Purchases</th>
                                <th>Sales</th>
                                <th>Wastage</th>
                                <th>Predicted</th>
                                <th>Closing</th>
                                <th>Difference</th>
                                <th>Variance</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($item['items'] as $id => $single)
                                <tr class="{{ $single['variance'] >= 0 ? 'success' : 'danger' }}">
                                    <td>{{ $id }}</td>
                                    <td>{{ $single['title'] }}</td>
                                    <td>£ {{ $single['purchases']['price'] }}</td>
                                    <td>{{ $single['last_stock'] }} {{ $single['units'] }}</td>
                                    <td>{{ $single['purchases']['value'] }} {{ $single['units'] }}</td>
                                    <td>{{ $single['sales'] }} {{ $single['units'] }}</td>
                                    <td>{{ $single['wastage'] }} {{ $single['units'] }}</td>
                                    <td>{{ $single['must_stock'] }} {{ $single['units'] }}</td>
                                    <td>{{ $single['current_stock'] }} {{ $single['units'] }}</td>
                                    <td>{{ $single['stock_difference'] }} {{ $single['units'] }}</td>
                                    <td>£ {{ $single['variance'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </li>
            @endforeach
            </ul>
        </div>
        <div class="col-lg-2">
            @section ('cchart2_panel_title','Wastes')
            @section ('cchart2_panel_body')
                <div style="max-width:400px; margin:0 auto;">@include('widgets.charts.cpiechart')</div>
            @endsection
            @include('widgets.panel', array('header'=>true, 'as'=>'cchart2'))
        </div>
    </div>
    <div class="row">
        <h1>Last period summary</h1>
        <hr>
        <div class="col-lg-8">
            <div class="tasks">
                <div class="task">
                    <div class="task-heading"><i class="fa-circle fa fa-fw {{ $summary['stock'] > 0 ? 'text-danger' : 'text-success' }}"></i> Closing stock</div>
                    <div class="task-content">
                        @if($summary['stock'] > 0)
                            <div class="task-line text-warning"><i class="fa-warning fa fa-fw"></i> {{ $summary['stock'] }} items have no closing stock. <a href="{{ action('StockCheckController@index', ['filter' => 'without_stock']) }}">Fix this!</a></div>
                        @else
                            <div class="task-line text-success"><i class="fa-check fa fa-fw"></i> Completed!</div>
                        @endif
                    </div>
                </div>
                <div class="task">
                    <div class="task-heading"><i class="fa-circle fa fa-fw {{ $summary['invoices'] == 0 || $summary['no_price'] > 0 ? 'text-danger' : 'text-success' }}"></i> Invoices</div>
                    <div class="task-content">
                        @if($summary['invoices'] == 0)
                            <div class="task-line text-danger"><i class="fa-warning fa fa-fw"></i> No purchases have been added to last period. <a href="{{ action('PurchasesController@index', ['stock_period' => $last_period, 'date_from' => $date_from, 'date_to' => $date_to]) }}">Fix this!</a></div>
                        @endif
                        @if($summary['no_price'] > 0)
                            <div class="task-line text-warning"><i class="fa-warning fa fa-fw"></i> {{ $summary['no_price'] }} item(s) have no price set. <a href="{{ action('ItemsController@prices') }}">Fix this!</a></div>
                        @endif
                        @if($summary['invoices'] > 0 && $summary['no_price'] == 0)
                            <div class="task-line text-success"><i class="fa-check fa fa-fw"></i> Completed!</div>
                        @endif
                    </div>
                </div>
                <div class="task">
                    <div class="task-heading"><i class="fa-circle fa fa-fw {{ $summary['sales'] == 0 ? 'text-danger' : 'text-success' }}"></i> Sales</div>
                    <div class="task-content">
                        @if($summary['sales'] == 0)
                            <div class="task-line text-danger"><i class="fa-warning fa fa-fw"></i> No sales have been added to this period. <a href="{{ action('SalesController@index', ['stock_period' => $last_period]) }}">Fix this!</a></div>
                        @else
                            <div class="task-line text-success"><i class="fa-check fa fa-fw"></i> Completed!</div>
                        @endif
                    </div>
                </div>
                <div class="task">
                    <div class="task-heading"><i class="fa-circle fa fa-fw {{ $summary['menu'] > 0 ? 'text-danger' : 'text-success' }}"></i> Menu</div>
                    <div class="task-content">
                        @if($summary['menu'] > 0)
                            <div class="task-line text-warning"><i class="fa-warning fa fa-fw"></i> {{ $summary['menu'] }} menu items are not assigned. <a href="{{ action('MenusController@index') }}">Fix this!</a></div>
                        @else
                            <div class="task-line text-success"><i class="fa-check fa fa-fw"></i> Completed!</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            @section ('cchart3_panel_title','TOP 10 Sales')
            @section ('cchart3_panel_body')
                <div style="max-width:400px; margin:0 auto;">@include('widgets.charts.cdonutchart')</div>
            @endsection
            @include('widgets.panel', array('header'=>true, 'as'=>'cchart3'))
        </div>
    </div>
</div>

@stop
@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $('#show_all_variances').on('click', function(e){
            e.preventDefault();
            $('.summary').slideToggle('fast');
        });
        $('.category-summary').on('click', function(e){
            e.preventDefault();
            $(this).parent().find('.table-container').slideToggle('fast');
        });

        var pdata = <?php echo json_encode($wastage); ?>;
        var cpie = document.getElementById("cpie").getContext("2d");
        new Chart(cpie).Pie(pdata, { responsive: true});

        var ddata = <?php echo json_encode($sales); ?>;
        var cdonut = document.getElementById("cdonut").getContext("2d");
        new Chart(cdonut).Doughnut(ddata, { responsive: true});
    });
</script>
@endpush