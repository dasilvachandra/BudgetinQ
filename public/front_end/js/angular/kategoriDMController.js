app.controller('kategoriDMController', function ($scope, $rootScope, $routeParams) {
    console.log($rootScope.pathname);
    function returnData(data) {
        console.log(data);
        $scope.gcPendapatan = data['gcPendapatan'];
        $scope.mGC = "6";
        $scope.$apply();
        tableJenisPendapatan(data['list_jenis_pendapatan']);
    }

    function tableJenisPendapatan(data) {
        // console.log(data);
        id = '#tableJenisPendapatan';
        if (!$.fn.DataTable.isDataTable(id)) {
            var table = $(id).addClass('nowrap').DataTable({
                "aaData": data,
                processing: true,
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                columns: [
                    { data: 'jenis_pendapatan', name: 'jenis_pendapatan' },
                    { data: 'jt', name: 'jt' },
                    { data: 'total', name: 'total' },
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
                            pathTime = '';
                            if ($rootScope.pathname[3] != undefined) {
                                pathTime = $rootScope.pathname[3];
                            }
                            url = '/danamasuk/kategori/' + row['id_jenis_pendapatan'] + "/" + pathTime;
                            return "<a href=" + url + ">" + data + "</a>";
                        }
                    },
                    { targets: 2, data: "jumlah", render: function (data, type, row, meta) { return formatRupiah(data) } },
                    {
                        targets: 3, data: "waktu", render: function (data, type, row, meta) {
                            var mydate = new Date(row['waktu']);
                            mY = monthYear(mydate);
                            return "<a href='/danamasuk/kategori/" + row['id_jenis_pendapatan'] + "/'>" + data + "</a>";
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

    function kategoriDMRefresh(data) {
        if (data['url'] !== undefined) {
            window.location = data['url'];
        }

    }

    $scope.kategoriDMStore = function (url, formID) {
        ajaxPost(url, $(formID).serialize(), kategoriDMRefresh);
    };

    $scope.kategoriDMDelete = function () {
        var table = $('#tableJenisPendapatan').DataTable();
        var data = table.row('.selected').data();
        if (data == null) {
            alert("Please select row!!");
        } else {
            // table.row(this).delete();

            var id = data['id_jenis_pendapatan'];

            var check = confirm("Are you sure you want to delete " + data['jenis_pendapatan'] + "?");
            if (check) {
                // table.$('tr.selected').css("background-color", "yellow");
                // table.$('tr.selected').fadeOut(100);
                // table.row('.selected').remove().draw(false);
                url = "/kategori/danamasuk/delete";
                data = {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    'id_jenis_pendapatan': id
                };
                ajaxPost(url, data, kategoriDMRefresh);
            }
        }


    }

    $scope.kategoriDMEdit = function () {
        function getDataEdit(data) {
            console.log(data);
            $scope.id_jenis_pendapatan = data['editData'][0]['id_jenis_pendapatan'];
            $scope.jenis_pendapatan = data['editData'][0]['jenis_pendapatan'];
            $scope.mGC = data['editData'][0]['group_category_id'];
            $scope.$apply();
            document.getElementsByClassName("kategoriDMEditModal")[0].click();
        }

        var table = $('#tableJenisPendapatan').DataTable();
        var data = table.row('.selected').data();
        if (data == null) {
            alert("Please select row!!");
        } else {
            var id = data['id_jenis_pendapatan'];
            if (confirm("Are you sure you want to Edit " + data['jenis_pendapatan'] + "?")) {
                url = "/kategori/danamasuk/edit/";

                data = {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    'id_jenis_pendapatan': id
                };
                ajaxPost(url, data, getDataEdit);
            }
        }
    }

    if ($rootScope.pathname.length == 3) {
        ajaxGet('/kategoriDMR', returnData);
    }
    if ($rootScope.pathname.length == 4) {
        ajaxGet('/kategoriDMR/' + $("#time").val(), returnData);
    }

    $('#time').change(function () {
        time = $("#time").val();
        url = '/kategori/danamasuk/' + time;
        console.log(url);
        window.location = url;
    });


});