@section('topbar2')
<div class="col-xl-3 col-md-6">
    <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pendapatan</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{$totalDanaMasuk}}</div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

<div class="card-header py-3 mb-4">
    <h6 class=" font-weight-bold text-primary">DAFTAR PENDAPATAN
        @if (isset($page)=='kategori')
            KATEGORI 
            <select ng-model="sjpd" ng-change="onChangeSJPG(sjpd)">
                @foreach($jenis_pendapatan as $show)
                    @php $active=""; @endphp
                    @if ($show->id_jenis_pendapatan==$id_jenis_pendapatan)
                        @php $active="selected"; @endphp
                    @endif
                <option value="{{$show->id_jenis_pendapatan}}" {{$active}} >{{$show->jenis_pendapatan}}</option>
                @endforeach
            </select>
        @endif
        {!!$title!!} 
    </h6>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover" id="tablePendapatan" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Deskripsi</th>
                <th>Jumlah</th>
                <th>Kategori</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
 
