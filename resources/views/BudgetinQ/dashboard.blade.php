@extends('BudgetinQ.master')
@section('title','Dashboard')
@section('topbar1')
@include('BudgetinQ.templates.selectMonth')
@stop
@section('content')
<div ng-controller="dashboardController as Ctrl">
    <!-- Content Card Total Pendapatan, Total Pengeluaran, Saldo, Maximum Per Hari -->
    @include('BudgetinQ.dashboard.card')
    <!-- Content Grafik & Diagram Bulat -->
    <div class="row">
        <!-- Area Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Dana Keluar Overview</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Dropdown Header:</div>
                            <a class="dropdown-item" href="#">Action</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary" > GROUP
                    <select ng-model="mGC"  ng-change="onChangeChartPie(mGC)">
                        <option disabled>-- Pilih Group --</option>
                        <option ng-repeat="s in gcPengeluaran" value="<%s.group_category_id%>">
                            <%s.group_category%>
                        </option>
                    </select>
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Dropdown Header:</div>
                            <a class="dropdown-item" href="#">Action</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div id="donut_chart" class="mt-0 dashboard-donut-chart"></div>
                    <div class="mt-4 text-center small" ng-repeat='row in gcPengeluaran' ng-if="mGC==row.group_category_id">
                        <span class="mr-2"  ng-repeat="s in category(cPengeluaran,row.group_category_id)" >
                                <i class="fas fa-circle" style="color:<%s.color%>;"></i> 
                                <a href=''><%s.jenis_pengeluaran%></a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Content Column -->
        <div class="col-lg-6 mb-4">
            <!-- Project Card Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Group Kategori Pendapatan</h6>
                </div>
                <div class="card-body">
                    @foreach($gcPendapatan as $i => $value)
                    <h4 class="small font-weight-bold">{{$value->group_category}} Rp {{strrev(implode('.',str_split(strrev(strval($value->total)),3)))}} <span class="float-right">{{$value->persen}}%</span>
                    </h4>
                    <div class="progress mb-4">
                        <div style="background-color:{{$value->color}};width: {{$value->persen}}%" class="progress-bar" role="progressbar"
                            aria-valuenow="{{$value->persen}}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Color System -->
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card bg-primary text-white shadow">
                        <div class="card-body">
                            Primary
                            <div class="text-white-50 small">#4e73df</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card bg-success text-white shadow">
                        <div class="card-body">
                            Success
                            <div class="text-white-50 small">#1cc88a</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card bg-info text-white shadow">
                        <div class="card-body">
                            Info
                            <div class="text-white-50 small">#36b9cc</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card bg-warning text-white shadow">
                        <div class="card-body">
                            Warning
                            <div class="text-white-50 small">#f6c23e</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card bg-danger text-white shadow">
                        <div class="card-body">
                            Danger
                            <div class="text-white-50 small">#e74a3b</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card bg-secondary text-white shadow">
                        <div class="card-body">
                            Secondary
                            <div class="text-white-50 small">#858796</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-6 mb-4">

            <!-- Illustrations -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Group Kategori Pengeluaran</h6>
                </div>

                <div class="card-body">
                    @foreach($gcPengeluaran as $i => $value)
                    <h4 class="small font-weight-bold">{{$value->group_category}} <span class="float-right">{{$value->persen}}%</span>
                    </h4>
                    <div class="progress mb-4">
                        <div style="background-color:{{$value->color}};width: {{$value->persen}}%" class="progress-bar" role="progressbar"
                            aria-valuenow="{{$value->persen}}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Approach -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Development Approach</h6>
                </div>
                <div class="card-body">
                    <p>SB Admin 2 makes extensive use of Bootstrap 4 utility classes in order to reduce CSS
                        bloat and poor
                        page performance. Custom CSS classes are used to create custom components and custom
                        utility
                        classes.</p>
                    <p class="mb-0">Before working with this theme, you should become familiar with the
                        Bootstrap
                        framework, especially the utility classes.</p>
                </div>
            </div>

        </div>
    </div>
</div>
@stop
@section('javascript')
<script src="{{asset('front_end/js/chartArea.js')}}"></script>
<script src="{{asset('front_end/js/chartPie.js')}}"></script>
<script src="{{asset('front_end/js/angular/dashboardController.js')}}"></script>
@stop