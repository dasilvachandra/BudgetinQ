<div class="card-header py-3 mb-4">
	<h6 class="m-0 font-weight-bold text-primary">Add Pemasukkan!</h6>
</div>
<form class="user" id="form_create_pendapatan" method="POST">
	{{csrf_field()}}
	<div class="form-group row">
		<div class="col-sm-6 mb-3 mb-sm-0">
			<b>Tanggal</b>
			<div class="input-group-append ">
				<span class="input-group-addon"><i class="material-icons">date_range</i></span>
				<input id="createSelectedTimePend" type="text" value="{{date('d F, Y')}}" autoComplete="off" value=""
					class=" datepickerForm form-control form-control-user" name="time" required
					placeholder="Please choose a date...">
			</div>
		</div>
		<div class="col-sm-6">
			<b>Deskripsi Pemasukkan</b>
			<div class="input-group-append ">
				<span class="input-group-addon"><i class="material-icons mr-0">label_outline</i></span>
				<input type="text" class="form-control form-control-user" ng-model="selectedTime" name="nama_pendapatan"
					required placeholder="Deskripsi Pemasukkan">
			</div>
		</div>
	</div>
	<div class="form-group">
		<b>Group Kategori</b>
		<div class="input-group form-float" ng-init="mGC='6'">
			<span class="input-group-addon"><i class="material-icons">dehaze</i></span>
			<select class="form-control " name="jenis_pendapatan" ng-model="mGC" ng-change="onChange(mGC)" required>
				<option>-- Pilih Kategori --</option>
				<option ng-repeat="s in gcPendapatan" value="<%s.group_category_id%>"><%s.group_category%>
				</option>
			</select>
		</div>
	</div>
	<div class="form-group row">
		<div class="col-sm-6 mb-3 mb-sm-0">
			<b>Kategori</b>
			<div class="input-group form-float">
				<span class="input-group-addon"><i class="material-icons">dehaze</i></span>
				<select ng-repeat='row in gcPendapatan' ng-if="mGC==row.group_category_id" class="form-control " id=""
					name="jenis_pendapatan" required>
					<option value="">-- Pilih Kategori --</option>
					<option ng-repeat="s in category(cPendapatan,row.group_category_id)"
						value="<%s.id_jenis_pendapatan%>">
						<%s.jenis_pendapatan%></option>
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
				<input id="input_rupiah" type="text" class="form-control form-control-user" name="jumlah" required
					placeholder="Jumlmah Dana">
			</div>
		</div>
	</div>
	<button id="button" type="submit" class="btn btn-success btn-user btn-block"
		ng-click="danamasukStore('/danamasuk/store','#form_create_pendapatan')" type="submit">Submit
		<i class="mdi-content-send right"></i></button>
</form>