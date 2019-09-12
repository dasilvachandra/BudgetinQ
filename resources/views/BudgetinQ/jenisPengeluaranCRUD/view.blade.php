@extends('BudgetinQ.master')
@section('title','Dana Masuk')
@section('topbar1')
@include('BudgetinQ.templates.selectMonth')
@stop
@section('content')
<div class="card o-hidden border-0 shadow-lg my-5" ng-controller="kategoriDKController as Ctrl">
<div class="card-body p-0">
        <!-- Nested Row within Card Body -->
        <div class="row">
            <div class="col-lg-12 shadow">
                <div class="p-4">

                    <a href="#" class="btn btn-primary btn-icon-split mb-2" data-toggle="modal"
                        data-target="#kategoriDKCreateModal">
                        <span class="icon text-white-50">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span class="text">Tambah</span>
                    </a>
                    <a href="#" class="btn btn-info btn-icon-split mb-2" ng-click="kategoriDKEdit()">
                        <span class="icon text-white-50">
                            <i class="fa fa-pencil-square-o"></i>
                        </span>
                        <span class="text">Edit</span>
                    </a>
                    <button class="kategoriDKEditModal" data-toggle="modal" data-target="#kategoriDKEditModal"
                        style="display: none;"></button>

                    <a href="#" class="btn btn-danger btn-icon-split mb-2" ng-click="kategoriDKDelete()">
                        <span class="icon text-white-50">
                            <i class="fas fa-trash"></i>
                        </span>
                        <span class="text">Hapus</span>
                    </a>

                    @include('BudgetinQ.jenisPengeluaranCRUD.table')
                    @include('BudgetinQ.jenisPengeluaranCRUD.create')
                    @include('BudgetinQ.jenisPengeluaranCRUD.edit')
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('javascript')
<script src="{{asset('front_end/js/angular/kategoriDKController.js')}}"></script>
@stop