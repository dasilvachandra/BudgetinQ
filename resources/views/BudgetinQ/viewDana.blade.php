@extends('BudgetinQ.master')
@section('title',$titleTransaksi)
@section('topbar1')
@include('BudgetinQ.templates.selectMonth')
@stop
@section('topbar2')
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total {{$titleTransaksi}}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$totalDana}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="card o-hidden border-0 shadow-lg my-5" ng-controller="{{$controller}} as Ctrl">

    <div class="card-body p-0">
        <!-- Nested Row within Card Body -->

        <div class="col-lg-12 shadow">
            <div class="p-3">
                <!-- TABLE -->
                <div class="card-header py-3 ">
                    <h6 class=" font-weight-bold text-primary">
                        {!!$title!!} 
                    </h6>
                </div>

                <div class="table-responsive">
                    <div class="mb-3 ml-2 mt-3">
                        <a href="#" class="btn-circle btn-primary" data-toggle="modal" data-target="#createModal">
                            <i class="fas fa-plus"></i>
                        </a>
                        <a href="#" class="btn-circle btn-info " ng-click="edit()">
                            <i class="fa fa-pencil-square-o"></i>
                        </a>
                        <button class="editModal" data-toggle="modal" data-target="#editModal"
                            style="display: none;"></button>
                        <a href="#" class="btn-circle btn-danger" ng-click="danaDelete()">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>


                    <table class="table table-bordered table-hover" id="table{{$titleTransaksi}}" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Deskripsi</th>
                                <th>Jumlah</th>
                                <th>Jenis Transaksi [kategori]</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('BudgetinQ.danaCreate')

    @if (isset($titleTransaksi)=='Pengeluaran')
        @include('BudgetinQ.danakeluar_CRUD.edit')
    @endif
    @if (isset($titleTransaksi)=='Pendapatan')
        @include('BudgetinQ.danamasuk_CRUD.create')
        @include('BudgetinQ.danamasuk_CRUD.edit')
    @endif
    
</div>





@stop
@section('javascript')
<script src="{{asset('front_end/js/angular/danakeluarController.js')}}"></script>
@stop
