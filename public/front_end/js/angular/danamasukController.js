app.controller('danamasukController', function ($scope, $rootScope, $routeParams) {
    $scope.category = function (data, gcID) {
        a = data.filter(function (b) {
            return b.group_category_id == gcID;
        });
        return a;
    };
    time = $("#time").val();
    function returnData(data) {
        console.log(data);
        $scope.cPendapatan = data['cPendapatan'];
        $scope.gcPendapatan = data['gcPendapatan'];
        tablePendapatan(data['list_pendapatan']);
        $scope.mGC = "6";
        $scope.$apply();
    }
    if ($rootScope.pathname.length == 3 || $rootScope.pathname.length == 2) {
        url = '/dmr/' + time
        redirectTimeForm('/danamasuk/');
        ajaxGet(url, returnData);
    }
    if ($rootScope.pathname.length == 4) {
        redirectTimeForm('/danamasuk/');
        url = '/dmr/' + time + "/" + $rootScope.pathname[3];
        ajaxGet(url, returnData);
    }

    // get data danamasuk by kategori
    if ($rootScope.pathname[2] == 'kategori') {
        $scope.sjpg = $rootScope.pathname[3];
        $scope.onChangeSJPG = function (value) {
            $rootScope.pathname.splice(3, 1, value);
            url = '/danamasuk/kategori/' + $rootScope.pathname[3] + "/" + $rootScope.pathname[4];
            // console.log($rootScope.pathname);
            window.location = url;
        }
        $('#time').change(function () {
            time = $("#time").val();
            $rootScope.pathname.splice(4, 1, time);
            url = '/danamasuk/kategori/' + $rootScope.pathname[3] + "/" + $rootScope.pathname[4];
            window.location = url;
        });
        url = '/dana/kategori/' + $rootScope.pathname[3] + "/" + $rootScope.pathname[4];
        console.log(url);
        ajaxGet(url, returnData);
    }


    function danamasukRefresh(data) {
        // console.log(data);
        if (data['pesan'] == '1') {
            // url = '/danamasuk/' + $rootScope.pathname[2] + "/" + data['day'];
            // window.location = window.location.href;
            window.location = data['link'];
        }
    }

    $scope.danamasukStore = function (url, formID) {
        ajaxPost(url, $(formID).serialize(), danamasukRefresh);
    };
    // TABLE
    function tablePendapatan(data) {
        // console.log(data);
        id = '#tablePendapatan';
        if (!$.fn.DataTable.isDataTable(id)) {
            var table = $(id).addClass('nowrap').DataTable({
                "aaData": data,
                processing: true,
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                columns: [
                    { data: 'waktu', name: 'waktu' },
                    { data: 'nama_pendapatan', name: 'nama_pendapatan' },
                    { data: 'jumlah', name: 'jumlah' },
                    { data: 'jenis_pendapatan', name: 'jenis_pendapatan' },
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
                            url = "<a href='/danamasuk/" + mY + "/" + dd + "'>" + data + "</a>";
                            return url;
                        }
                    },
                    { targets: 2, data: "jumlah", render: function (data, type, row, meta) { return formatRupiah(data) } },
                    {
                        targets: 3, data: "waktu", render: function (data, type, row, meta) {
                            var mydate = new Date(row['waktu']);
                            mY = monthYear(mydate);
                            return "<a href='/danamasuk/kategori/" + row['id_jenis_pendapatan'] + "/ " + mY + "/'>" + data + "</a>";
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

    $scope.danamasukDelete = function () {
        var table = $('#tablePendapatan').DataTable();
        var data = table.row('.selected').data();
        if (data == null) {
            alert("Please select row!!");
        } else {
            // table.row(this).delete();

            var id = data['id_pendapatan'];
            var check = confirm("Are you sure you want to delete this?");
            if (check) {
                table.$('tr.selected').css("background-color", "yellow");
                table.$('tr.selected').fadeOut(100);
                // table.row('.selected').remove().draw(false);
                url = "/danamasuk/delete";
                data = {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                };
                ajaxPost(url, data, danamasukRefresh);
            }
        }


    }

    $scope.danamasukEdit = function () {
        function getDataEdit(data) {
            console.log(data['editData']);
            var mydate = new Date(data['editData'][0]['waktu']);
            $scope.editTanggal = dateMonthYear(mydate);
            $scope.id_pendapatan = data['editData'][0]['id_pendapatan'];
            $scope.editNamaPendapatan = data['editData'][0]['nama_pendapatan'];
            $scope.mGC = data['editData'][0]['group_category_id'];
            $scope.wmGC = data['editData'][0]['group_category_id'];
            $scope.mC = data['editData'][0]['id_jenis_pendapatan'];
            $scope.wmC = data['editData'][0]['id_jenis_pendapatan'];
            $scope.editPrice = data['editData'][0]['jumlah'];
            $scope.swPeng = data['editData'][0]['group_category'].toLowerCase();
            id = '#' + $scope.swPeng + "_id";
            // console.log($scope.swPeng);
            $(id).val(data['editData'][0]['id_jenis_pendapatan']);
            $scope.whenGroupCategory = data['editData'][0]['group_category'].toLowerCase();
            $scope.m_jenis_pendapatan = data['editData'][0]['id_jenis_pendapatan'];
            $scope.$apply();
            input_rupiah("editInputRupiah");
            document.getElementsByClassName("danamasukEditModal")[0].click();
            // datePickerForm('.datepickerForm');

            // tablePendapatan(data['pendapatan']);
        }

        var table = $('#tablePendapatan').DataTable();
        var data = table.row('.selected').data();
        if (data == null) {
            alert("Please select row!!");
        } else {
            var id = data['id_pendapatan'];
            if (confirm("Are you sure you want to Edit this?")) {
                url = "/danamasuk/edit/";
                // console.log(url);
                data = {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    'id_pendapatan': id
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
            window.location = "/kategori/danamasuk";
        }
    }


    function redirectTimeForm(url) {
        $('#time').change(function () {
            time = $('#time').val();
            window.location = url + time;
        });
    }



});