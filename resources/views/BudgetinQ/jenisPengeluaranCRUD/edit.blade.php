<div class="modal fade" id="kategoriDKEditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
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
                    <form class="user" id="form_edit" method="POST">
                        {{csrf_field()}}
                        <input type="hidden" class="form-control form-control-user" ng-model="id_jenis_pengeluaran" ng-value="id_jenis_pengeluaran"
                                        name="id_jenis_pengeluaran" required placeholder="Deskripsi Pengeluaran">
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <b>Nama Kategori</b>
                                <div class="input-group-append ">
                                    <span class="input-group-addon"><i
                                            class="material-icons mr-0">label_outline</i></span>
                                    <input type="text" class="form-control form-control-user" ng-model="jenis_pengeluaran" ng-value="jenis_pengeluaran"
                                        name="jenis_pengeluaran" required placeholder="Deskripsi Pengeluaran">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <b>Group Kategori</b>
                            <div class="input-group form-float" ng-init="mGC='1'">
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
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button id="button" type="submit" class="btn btn-success btn-user btn-block"
                    ng-click="kategoriDKStore('/kategori/danakeluar/update','#form_edit')" type="submit">Submit
                    <i class="mdi-content-send right"></i></button>
            </div>
        </div>
    </div>
</div>