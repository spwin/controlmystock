@extends('layouts.dashboard')
@section('page_heading', $title.' list')
@section('section')
    <div class="col-sm-12">
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                {{ Session::get('flash_message') }}
            </div>
        @endif
        @if(count($periods) > 0)
        <div class="row">
            <div class="col-sm-6">
                @section ('table_panel_title', $title)
                @section ('table_panel_body')
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Period number</th>
                                <th>Period starts</th>
                                <th>Period ends</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($periods as $period)
                                <tr>
                                    <td>Stock period #{{ $period->number }}</td>
                                    <td>{{ $period->date_from }}</td>
                                    <td>{{ $period->date_to ? $period->date_to : 'current' }}</td>
                                    <td>
                                        @if($period->date_to)
                                            <a href="" class="btn btn-primary btn-xs">Period info</a>
                                        @else
                                            <a href="{{ action('StockPeriodsController@close', $period->id) }}" class="btn btn-xs btn-success">Close period</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                @endsection
                @include('widgets.panel', array('header'=>true, 'as'=>'table'))
            </div>
        </div>
        @else
            <a href="{{ action ('StockPeriodsController@create') }}" class="mb-20px block"><i class="fa fa-plus-circle fa-fw"></i>Begin new  {{ $title }}</a>
        @endif
    </div>
@stop