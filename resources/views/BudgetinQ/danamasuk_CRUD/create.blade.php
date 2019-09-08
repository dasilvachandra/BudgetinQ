<div class="text-center">
	<h1 class="h4 text-gray-900 mb-4">ADD DANA MASUK</h1>
</div>
<form class="user" id="form_create_pendapatan" method="POST">
	{{csrf_field()}}
	<div class="form-group row">
		<div class="col-sm-6">
			<b>Tanggal</b>
			<div class="input-group-append ">
				<span class="input-group-addon">
					<i class="material-icons">date_range</i>
				</span>
				<div class="form-line">
					<input id="createSelectedTimePend" type="text" value="{{date('d F, Y')}}" autoComplete="off"
						value="" class="datepicker datepickerForm form-control" name="time" required
						placeholder="Please choose a date...">
				</div>
			</div>

		</div>
		<div class="col-sm-6">
			<b>Deskripsi Pemasukkan</b>
			<div class="input-group form-float">
				<div class="input-group-append ">
					<span class="input-group-addon">
						<i class="material-icons mr-0">label_outline</i>
					</span>
					<div class="form-line">
						<input type="text" class="form-control" ng-model="selectedTime" name="nama_pendapatan" required
							placeholder="Dana Name">
						<!-- <label class="form-label">Name</label> -->
					</div>
				</div>
			</div>

		</div>
	</div>
	<div class="form-group row">
		<div class="col-sm-12">
			<b>Group Category</b>
			<div class="input-group form-float" ng-init="mGC='6'">
				<span class="input-group-addon">
					<i class="material-icons">dehaze</i>
				</span>
				<select class="form-control show-tick" name="jenis_pendapatan" ng-model="mGC" ng-change="onChange(mGC)"
					required>
					<option>-- Pilih Kategori --</option>
					<option ng-repeat="s in gcPendapatan" value="<%s.group_category_id%>"><%s.group_category%>
					</option>
				</select>
			</div>
		</div>
	</div>
	<div class="form-group row">
		<div class="col-sm-6">
			<b>Category</b>
			<div class="input-group form-float">
				<div class="input-group-append ">
					<span class="input-group-addon">
						<i class="material-icons">dehaze</i>
					</span>
					<div ng-repeat='row in gcPendapatan'>
						<div ng-if="mGC==row.group_category_id">
							<select class="form-control show-tick" id="" name="jenis_pendapatan" required>
								<option value="">-- Pilih Kategori --</option>
								<option ng-repeat="s in category(cPendapatan,row.group_category_id)"
									value="<%s.id_jenis_pendapatan%>"><%s.jenis_pendapatan%></option>
								<option value="1" style="font-weight:bold;">ADD/EDIT Category</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<b>Jumlah Dana</b>
			<div class="input-group form-float">
				<div class="input-group-append ">
					<span class="input-group-addon">
						Rp
					</span>
					<div class="form-line">
						<input id="input_rupiah" type="text" class="form-control" name="jumlah" required
							placeholder="Jumlmah Dana">
						<!-- <label class="form-label">Name</label> -->
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button id="button" type="submit" class="btn btn-primary waves-effect"
			ng-click="storependapatan('/app_keuangan/pendapatan/store','#form_create_pendapatan')" type="submit">Submit
			<i class="mdi-content-send right"></i></button>
		<button id="closeModPendCreate" type="button" class="btn btn-link waves-effect"
			data-dismiss="modal">CLOSE</button>
		<button type="reset" class="btn bg-red btn-link waves-effect">Reset</button>
	</div>
</form>