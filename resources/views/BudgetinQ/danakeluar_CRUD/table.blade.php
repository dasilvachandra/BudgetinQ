
<div class="card-header py-3 mb-4">
    <h6 class="m-0 font-weight-bold text-primary">DAFTAR PENGELUARAN
        @if (isset($page)=='kategori')
            <select ng-model="sjpg" ng-change="onChangeSJPG(sjpg)">
                @foreach($jenis_pengeluaran as $show)
                    @php $active=""; @endphp
                    @if ($show->id_jenis_pengeluaran==$id_jenis_pengeluaran)
                        @php $active="selected"; @endphp
                    @endif
                <option value="{{$show->id_jenis_pengeluaran}}" {{$active}} >{{$show->jenis_pengeluaran}}</option>
                @endforeach
            </select>
        @endif
        {!!$title!!} 
    </h6>
</div>
<div class="table-responsive">
    <table class="table table-bordered table-hover" id="tablePengeluaran" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Deskripsi</th>
                <th>Jumlah</th>
                <th>Category</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>