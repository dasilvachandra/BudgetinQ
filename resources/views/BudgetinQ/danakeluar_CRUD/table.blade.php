
<div class="card-header py-3 mb-4">
    <h6 class="m-0 font-weight-bold text-primary"> {{$title}}
    {!! Form::select('size', array('L' => 'Large', 'S' => 'Small'), 'S'); !!}
        @if (isset($page)=='kategori')
            <select>
                @foreach($jenis_pengeluaran as $show)
                    @php $active="-"; @endphp
                    @if ($show->id_jenis_pengeluaran==$id_jenis_pengeluaran)
                        @php $active="active"; @endphp
                    @endif
                <option value="{{$show->id_jenis_pengeluaran}}" {{$active}} >{{$show->jenis_pengeluaran}}</option>
                @endforeach
            </select>
        @endif
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