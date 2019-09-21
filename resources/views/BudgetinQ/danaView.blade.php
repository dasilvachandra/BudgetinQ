@extends('BudgetinQ.master')
@section('title',$titleTransaksi)
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{$title}}</h1>
</div>

<div class="card o-hidden border-0 shadow-lg" ng-controller="{{$controller}} as Ctrl">

    <div class="card-body p-0">
        <!-- Nested Row within Card Body -->
        <div class="col-lg-12 shadow">
            <div class="p-3">
                <!-- TABLE -->
                <div class="card-header ">
                    <div class="p-2">
                        <div class="input-group date bs_datepicker_component_container" id="selectMonth">
                            <div class="input-group-append ">
                                <button class="btn btn-primary" type="button" style="z-index: 0;">
                                    <i class="fas fa-calendar fa-sm"></i>
                                </button>
                                <div class="form-line">
                                    <input type="text" class="form-control bg-light border-0s small time" id="time"
                                        value="{{$monthYear}}" autoComplete="off" placeholder="Please choose a date...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- BUTTON CRUD-->
                <div class="mt-3 d-flex flex-row border border-warning rounded">
                    <div class="p-2">
                        <a href="#" class="btn-circle btn-primary" data-toggle="modal" data-target="#createModal">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                    <div class="p-2">
                        <a href="#" class="btn-circle btn-info " ng-click="edit()">
                            <i class="fa fa-pencil-square-o"></i>
                        </a>
                    </div>

                    <div class="p-2">
                        <button class="editModal" data-toggle="modal" data-target="#editModal"
                            style="display: none;"></button>
                        <a href="#" class="btn-circle btn-danger" ng-click="danaDelete()">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                    <div class="ml-auto p-2">
                        <a href="/generatePengeluaranPDF/{{$monthYear}}"
                            class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div>


                </div>

                <div class="d-flex justify-content-center mt-3">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Pendapatan</th>
                                    <th scope="col">Pengeluaran</th>
                                    <th scope="col">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="spinner-grow" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        <div class="half d-none"><% totalDana %></div>
                                    </td>
                                    <td>
                                        <div class="spinner-grow" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        <div class="half d-none"><% totalDana %></div>
                                    </td>
                                    <td>
                                        <div class="spinner-grow" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        <div class="half d-none"><% totalDana %></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover" id="table{{$titleTransaksi}}" width="100%"
                        cellspacing="0">
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
    @include('BudgetinQ.danaEdit')
    @if (isset($titleTransaksi)=='Pengeluaran')

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