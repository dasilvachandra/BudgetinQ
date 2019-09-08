app.controller('danakeluarController', function ($scope, $rootScope, $routeParams) {
    $scope.category = function (data, gcID) {
        a = data.filter(function (b) {
            return b.group_category_id == gcID;
        });
        return a;
    };
    function returnData(data) {
        $scope.cPengeluaran = data['cPengeluaran'];
        $scope.gcPengeluaran = data['gcPengeluaran'];
        tablePengeluaran(data['list_pengeluaran']);
        $scope.$apply();
    }

    if ($rootScope.pathname.length == 3 || $rootScope.pathname.length == 2) {
        value = $("#time").val();
        url = '/dkr/' + value
        ajaxGet(url, returnData);
    }
    if ($rootScope.pathname.length == 4) {
        url = '/dkr/' + $("#time").val() + "/" + $rootScope.pathname[3];
        ajaxGet(url, returnData);
    }

    function danakeluarRefresh(data) {
        if (data['pesan'] == '1') {
            window.location = window.location.href;
        }
    }

    $scope.danakeluarStore = function (url, formID) {
        ajaxPost(url, $(formID).serialize(), danakeluarRefresh);
    };
    // TABLE
    function tablePengeluaran(data) {
        // console.log(data);
        id = '#tablePengeluaran';
        if (!$.fn.DataTable.isDataTable(id)) {
            var table = $(id).addClass('nowrap').DataTable({
                "aaData": data,
                processing: true,
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                columns: [
                    { data: 'waktu', name: 'waktu' },
                    { data: 'nama_pengeluaran', name: 'nama_pengeluaran' },
                    { data: 'jumlah', name: 'jumlah' },
                    { data: 'jenis_pengeluaran', name: 'jenis_pengeluaran' },
                ],
                language: {
                    searchPlaceholder: "Search..."
                },
                columnDefs: [
                    {
                        targets: 0, data: "waktu", render: function (data, type, row, meta) {
                            var mydate = new Date(data);
                            var dd = mydate.getDate();
                            mY = monthYear(mydate);
                            url = "<a href='/danakeluar/" + mY + "/" + dd + "'>" + data + "</a>";
                            return url;
                        }
                    },
                    { targets: 2, data: "jumlah", render: function (data, type, row, meta) { return formatRupiah(data) } },
                    {
                        targets: 3, data: "waktu", render: function (data, type, row, meta) {
                            return "<a href='/GC/" + row['group_category_id'] + "/ " + data + "'>" + data + "</a>";
                        }
                    },
                    { width: 200, targets: 0 },
                    { width: 400, targets: 1 }
                ]
            });
        } else {
            table = $(id).DataTable();
            table.clear();
            table.rows.add(data);
            table.draw();
            selectCell(id);
        }
        selectCell(id);
    }

    $scope.danakeluarDelete = function () {
        var table = $('#tablePengeluaran').DataTable();
        var data = table.row('.selected').data();
        if (data == null) {
            alert("Please select row!!");
        } else {
            // table.row(this).delete();

            var id = data['id_pengeluaran'];
            var check = confirm("Are you sure you want to delete this?");
            if (check) {
                table.$('tr.selected').css("background-color", "yellow");
                table.$('tr.selected').fadeOut(2000);
                // table.row('.selected').remove().draw(false);
                url = "/danakeluar/delete";
                data = {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                };
                ajaxPost(url, data, danakeluarRefresh);
            }
        }


    }

    $scope.danakeluarEdit = function () {
        function getDataEdit(data) {
            console.log(data['editData']);
            var mydate = new Date(data['editData'][0]['waktu']);
            $scope.editTanggal = dateMonthYear(mydate);
            $scope.id_pengeluaran = data['editData'][0]['id_pengeluaran'];
            $scope.editNamaPengeluaran = data['editData'][0]['nama_pengeluaran'];
            $scope.mGC = data['editData'][0]['group_category_id'];
            $scope.wmGC = data['editData'][0]['group_category_id'];
            $scope.mC = data['editData'][0]['id_jenis_pengeluaran'];
            $scope.wmC = data['editData'][0]['id_jenis_pengeluaran'];
            $scope.editPrice = data['editData'][0]['jumlah'];
            $scope.swPeng = data['editData'][0]['group_category'].toLowerCase();
            id = '#' + $scope.swPeng + "_id";
            // console.log($scope.swPeng);
            $(id).val(data['editData'][0]['id_jenis_pengeluaran']);
            $scope.whenGroupCategory = data['editData'][0]['group_category'].toLowerCase();
            $scope.m_jenis_pengeluaran = data['editData'][0]['id_jenis_pengeluaran'];
            $scope.$apply();
            input_rupiah("editInputRupiah");
            document.getElementsByClassName("danakeluarEditModal")[0].click();
            // datePickerForm('.datepickerForm');

            // tablePengeluaran(data['pengeluaran']);
        }

        var table = $('#tablePengeluaran').DataTable();
        var data = table.row('.selected').data();
        if (data == null) {
            alert("Please select row!!");
        } else {
            var id = data['id_pengeluaran'];
            if (confirm("Are you sure you want to Edit this?")) {
                url = "/danakeluar/edit/" + id;
                ajaxGet(url, getDataEdit);
            }
        }
    }

    $scope.GConChange = function (a, b, value) {
        if (a != b) {
            $scope.mC = '';
        } else {
            $scope.mC = value;
        }
    }

    $scope.ConChange = function (val) {
        if (val == '1') {
            window.location = "/kategori/danakeluar";
        }
    }



});