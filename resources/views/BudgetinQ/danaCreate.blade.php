<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah {{$titleTransaksi}}</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="p-3">
                    <form class="user" id="form_create" method="POST">
                        {{csrf_field()}}
                        <div class="form-group">
                            <b>Tanggal</b>
                            <div class="input-group-append ">
                                <span class="input-group-addon"><i class="material-icons">date_range</i></span>
                                <input id="createSelectedTimePend" type="text" value="{{date('d F, Y')}}"
                                    autoComplete="off" value="" class=" datepickerForm form-control form-control-user"
                                    name="time" required placeholder="Please choose a date...">
                            </div>
                        </div>
                        <div class="form-group">
                            <b>Nama {{$titleTransaksi}}</b>
                            <div class="input-group-append ">
                                <span class="input-group-addon"><i class="material-icons mr-0">label_outline</i></span>
                                <input type="text" class="form-control form-control-user" ng-model="selectedTime"
                                    name="title" required placeholder="Nama {{$titleTransaksi}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <b>Jenis Transaksi</b>
                            <div class="input-group form-float">
                                <span class="input-group-addon"><i class="material-icons">dehaze</i></span>
                                <select class="form-control " name="group_category" ng-model="mGC"
                                    ng-change="onChange(mGC)" required>
                                    <option>-- Pilih Jenis Transaksi --</option>
                                    <option ng-repeat="s in jenis_transaksi" value="<%s.group_category_id%>">
                                        <%s.group_category%>
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <b>Kategori</b>
                                <div class="input-group form-float">
                                    <span class="input-group-addon"><i class="material-icons">dehaze</i></span>
                                    <select ng-repeat='row in jenis_transaksi' ng-if="mGC==row.group_category_id"
                                        class="form-control" ng-model="mC" ng-change="ConChange(mC)" name="id_kategori"
                                        required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <option ng-repeat="s in findCategoryByID(kategori,row.group_category_id)"
                                            value="<%s.id_kategori%>">
                                            <%s.jenis_pengeluaran%></option>
                                        <option value="1" style="font-weight:bold;"><%opsi_add_or_edit%></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <b>Jumlah Dana</b>
                                <div class="input-group-append ">
                                    <span class="input-group-addon">
                                        Rp
                                    </span>
                                    <input id="input_rupiah" type="text" class="form-control form-control-user"
                                        name="jumlah" required placeholder="Jumlmah Dana">
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button id="button" type="submit" class="btn btn-success btn-user btn-block"
                    ng-click="store('#form_create')" type="submit">Submit
                    <i class="mdi-content-send right"></i></button>
            </div>
        </div>
    </div>
</div>