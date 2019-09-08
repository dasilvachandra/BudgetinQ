<div class="modal fade" id="danakeluarEditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Pengeluaran</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="p-4">
                    <form class="user" id="form_edit_pengeluaran" method="POST">
                        {{csrf_field()}}
                        <input type="hidden" id="id_pengeluaran" ng-value="id_pengeluaran" name="id_pengeluaran" value="">
                        <div class="form-group row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <b>Tanggal</b>
                                <div class="input-group-append ">
                                    <span class="input-group-addon"><i class="material-icons">date_range</i></span>
                                    <input id="editTanggal" type="text" value="{{date('d F, Y')}}" ng-model="editTanggal" ng-value="editTanggal"
                                        autoComplete="off" value=""
                                        class=" datepickerForm form-control form-control-user" name="time" required
                                        placeholder="Please choose a date...">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <b>Deskripsi Pengeluaran</b>
                                <div class="input-group-append ">
                                    <span class="input-group-addon"><i
                                            class="material-icons mr-0">label_outline</i></span>
                                    <input type="text" id="editNamaPengeluaran" class="form-control form-control-user" ng-model="editNamaPengeluaran" ng-value="editNamaPengeluaran"
                                        name="nama_pengeluaran" required placeholder="Deskripsi Pengeluaran">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <b>Group Kategori</b>
                            <div class="input-group form-float" ng-init="">
                                <span class="input-group-addon"><i class="material-icons">dehaze</i></span>
                                <select class="form-control " name="group_category" ng-model="mGC" ng-change="GConChange(wmGC,mGC,wmC)" required>
                                    <option>-- Pilih Kategori --</option>
                                    <option ng-repeat="s in gcPengeluaran" value="<%s.group_category_id%>">
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
                                    
                                    <select  ng-repeat='row in gcPengeluaran' ng-if="mGC==row.group_category_id"
                                        class="form-control " id=""  ng-model="mC" ng-change="ConChange(mC)" name="id_jenis_pengeluaran" required>
                                        
                                        <option value="">-- Pilih Kategori --</option>
                                        <option ng-repeat="s in category(cPengeluaran,row.group_category_id)"
                                            value="<%s.id_jenis_pengeluaran%>">
                                            <%s.jenis_pengeluaran%></option>
                                        <option value="1" style="font-weight:bold;">ADD/EDIT Category</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <b>Jumlah Dana</b>
                                <div class="input-group-append ">
                                    <span class="input-group-addon">
                                        Rp
                                    </span>
                                    <input id="editInputRupiah" type="text" ng-model="editPrice" class="form-control form-control-user"
                                        name="jumlah" ng-value="editPrice" required placeholder="Jumlmah Dana">
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button id="button" type="submit" class="btn btn-success btn-user btn-block"
                    ng-click="danakeluarStore('/danakeluar/update','#form_edit_pengeluaran')" type="submit">Submit
                    <i class="mdi-content-send right"></i></button>
            </div>
        </div>
    </div>
</div>