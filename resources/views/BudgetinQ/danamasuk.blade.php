@extends('BudgetinQ.master')
@section('title','Dana Masuk')
@section('content')
<div class="card o-hidden border-0 shadow-lg my-5" ng-controller="danamasukController as Ctrl">
    <div class="card-body p-0">
        <!-- Nested Row within Card Body -->
        <div class="row">
            <div class="col-lg-6 shadow">
                <div class="p-4">
                    @include('BudgetinQ.danamasuk_CRUD.create')
                </div>
            </div>
            <div class="col-lg-6 shadow">
                <div class="p-4">
                    @include('BudgetinQ.danamasuk_CRUD.table')
                </div>
            </div>
        </div>
    </div>
</div>

@stop
@section('javascript')
<script src="{{asset('front_end/js/angular/danamasukController.js')}}"></script>
@stop