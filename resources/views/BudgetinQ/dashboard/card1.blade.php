<div class="row" >
    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pemasukkan</div>
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
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Pengeluaran</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$danakeluar}}</div>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Saldo</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$saldo}}</div>
                    </div>
                    <div class="col-auto">
                    <i class=" fa-2x text-gray-300"><b>Rp</b></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Requests Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tersisa {{$sisaHari}} Hari Lagi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$maxperhari}}/<sub>Hari<sub></div>
                    </div>
                    <div class="col-auto">
                        <i class=" fa-2x text-gray-300"><b>Rp</b></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>