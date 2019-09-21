@extends('BudgetinQ.master')
@section('title','Dana Keluar')
@section('topbar1')
@include('BudgetinQ.templates.selectMonth')
@stop


@section('content')
<div class="card o-hidden border-0 shadow-lg my-5" ng-controller="danakeluarController as Ctrl">

    <div class="card-body p-0">
        <!-- Nested Row within Card Body -->

        <div class="col-lg-12 shadow">
            <div class="p-3">
                @include('BudgetinQ.danakeluar_CRUD.table')
            </div>
        </div>
    </div>
    @include('BudgetinQ.danakeluar_CRUD.create')
    @include('BudgetinQ.danakeluar_CRUD.edit')
</div>

@stop
@section('javascript')
<script src="{{asset('front_end/js/angular/danakeluarController.js')}}"></script>
@stop