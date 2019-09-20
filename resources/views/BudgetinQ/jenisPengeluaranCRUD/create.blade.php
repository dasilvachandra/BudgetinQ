<div class="modal fade" id="kategoriDKCreateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Kategori Pengeluaran</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="p-4">
                    <form class="user" id="form_create" method="POST">
                        {{csrf_field()}}
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <b>Kategori</b>
                                <div class="input-group-append ">
                                    <span class="input-group-addon"><i
                                            class="material-icons mr-0">label_outline</i></span>
                                    <input type="text" class="form-control form-control-user" ng-model="selectedTime"
                                        name="jenis_pengeluaran" required placeholder="Nama Kategori Pengeluaran">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <b>Jenis Transaksi</b>
                            <div class="input-group form-float">
                                <span class="input-group-addon"><i class="material-icons">dehaze</i></span>
                                <select class="form-control " name="group_category_id" ng-model="mGC"
                                    ng-change="onChange(mGC)" required>
                                    <option>-- Pilih Kategori --</option>
                                    <option ng-repeat="s in gcPengeluaran" value="<%s.group_category_id%>">
                                        <%s.group_category%>
                                    </option>
                                </select>
                            </div>
                        </div>
                        <p>Note : *<%cariNote(gcPengeluaran,mGC)%></p>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button id="button" type="submit" class="btn btn-success btn-user btn-block"
                    ng-click="kategoriDKStore('/kategori/danakeluar/store','#form_create')" type="submit">Submit
                    <i class="mdi-content-send right"></i></button>
            </div>
        </div>
    </div>
</div>