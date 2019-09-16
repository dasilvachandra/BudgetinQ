

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
                <th>Group</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
 
