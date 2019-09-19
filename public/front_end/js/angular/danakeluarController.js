app.controller('danakeluarController', function ($scope, $rootScope, $routeParams) {
    $scope.category = function (data, gcID) {
        a = data.filter(function (b) {
            return b.group_category_id == gcID;
        });
        return a;
    };
    time = $("#time").val();
    function returnData(data) {
        $scope.cPengeluaran = data['cPengeluaran'];
        $scope.gcPengeluaran = data['gcPengeluaran'];
        tablePengeluaran(data['list_pengeluaran']);
        $scope.$apply();
    }
    console.log($rootScope.pathname.length);
    if ($rootScope.pathname.length == 3 || $rootScope.pathname.length == 2) {
        url = '/dkr/' + time
        redirectTimeForm('/danakeluar/');
        ajaxGet(url, returnData);
    }
    if ($rootScope.pathname.length == 4) {
        redirectTimeForm('/danakeluar/');
        url = '/dkr/' + time + "/" + $rootScope.pathname[3];
        ajaxGet(url, returnData);
    }

    // get data danakeluar by kategori
    if ($rootScope.pathname[2] == 'kategori') {
        $scope.sjpg = $rootScope.pathname[3];
        $scope.onChangeSJPG = function (value) {
            $rootScope.pathname.splice(3, 1, value);
            url = '/danakeluar/kategori/' + $rootScope.pathname[3] + "/" + $rootScope.pathname[4];
            // console.log($rootScope.pathname);
            window.location = url;
        }
        $('#time').change(function () {
            time = $("#time").val();
            $rootScope.pathname.splice(4, 1, time);
            url = '/danakeluar/kategori/' + $rootScope.pathname[3] + "/" + $rootScope.pathname[4];
            window.location = url;
        });
        url = '/danaDK/kategori/' + $rootScope.pathname[3] + "/" + $rootScope.pathname[4];
        console.log(url);
        ajaxGet(url, returnData);
    }


    function danakeluarRefresh(data) {
        // console.log(data);
        if (data['pesan'] == '1') {
            // url = '/danakeluar/' + $rootScope.pathname[2] + "/" + data['day'];
            // window.location = window.location.href;
            window.location = data['link'];
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
                    { data: 'group_category', name: 'group_category' },
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
                            var mydate = new Date(row['waktu']);
                            mY = monthYear(mydate);
                            return "<a href='/danakeluar/kategori/" + row['id_jenis_pengeluaran'] + "/ " + mY + "/'>" + data + "</a>";
                        }
                    },
                    { width: 80, targets: 0 },
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
                table.$('tr.selected').fadeOut(100);
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
            // console.log(data['editData']);
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
                url = "/danakeluar/edit/";
                // console.log(url);
                data = {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    'id_pengeluaran': id
                };
                ajaxPost(url, data, getDataEdit);
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


    function redirectTimeForm(url) {
        $('#time').change(function () {
            time = $('#time').val();
            window.location = url + time;
        });
    }



});