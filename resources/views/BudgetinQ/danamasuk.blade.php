@extends('BudgetinQ.master')
@section('title','Dana Masuk')
@section('content')
<div class="row" ng-controller="danamasukController as Ctrl">
    <div class="col-xl-6 col-md-12 mb-8">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    @include('BudgetinQ.danamasuk_CRUD.create')
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('javascript')
<script src="{{asset('front_end/js/angular/danamasukController.js')}}"></script>
@stop