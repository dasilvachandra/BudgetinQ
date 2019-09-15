@extends('BudgetinQ.master')
@section('title','Dana Keluar')
@section('topbar1')
@include('BudgetinQ.templates.selectMonth')
@stop


@section('content')
<div class="row">
    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Saldo
                            ({{$monthBefore}})</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$saldoBulanLalu}}</div>
                    </div>
                    <div class="col-auto">
                        <i class=" fa-2x text-gray-300"><b>Rp</b></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pendapatan
                            (Monthly)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$danamasuk}}</div>
                    </div>
                    <div class="col-auto">
                        <i class=" fa-2x text-gray-300"><b>Rp</b></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total
                            ({{$monthYear}})</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$totalDanaMasuk}}</div>
                    </div>
                    <div class="col-auto">
                        <i class=" fa-2x text-gray-300"><b>Rp</b></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card o-hidden border-0 shadow-lg my-5" ng-controller="danamasukController as Ctrl">

    <div class="card-body p-0">
        <!-- Nested Row within Card Body -->

        <div class="col-lg-12 shadow">
            <div class="p-4">



                <a href="#" class="btn btn-primary btn-icon-split mb-2" data-toggle="modal"
                    data-target="#danamasukCreateModal">
                    <span class="icon text-white-50">
                        <i class="fas fa-plus"></i>
                    </span>
                    <span class="text">Tambah</span>
                </a>
                <a href="#" class="btn btn-info btn-icon-split mb-2" ng-click="danamasukEdit()">
                    <span class="icon text-white-50">
                        <i class="fa fa-pencil-square-o"></i>
                    </span>
                    <span class="text">Edit</span>
                </a>
                <button class="danamasukEditModal" data-toggle="modal" data-target="#danamasukEditModal"
                    style="display: none;"></button>

                <a href="#" class="btn btn-danger btn-icon-split mb-2" ng-click="danamasukDelete()">
                    <span class="icon text-white-50">
                        <i class="fas fa-trash"></i>
                    </span>
                    <span class="text">Hapus</span>
                </a>


                @include('BudgetinQ.danamasuk_CRUD.table')
            </div>
        </div>
    </div>
    @include('BudgetinQ.danamasuk_CRUD.create')
    @include('BudgetinQ.danamasuk_CRUD.edit')
</div>

@stop
@section('javascript')
<script src="{{asset('front_end/js/angular/danamasukController.js')}}"></script>
@stop