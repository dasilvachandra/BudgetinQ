app.controller('kategoriDKController', function ($scope, $rootScope, $routeParams) {
    console.log($rootScope.pathname);
    function returnData(data) {
        console.log(data);
        tableJenisPengeluaran(data['list_jenis_pengeluaran']);
    }

    function tableJenisPengeluaran(data) {
        // console.log(data);
        id = '#tableJenisPengeluaran';
        if (!$.fn.DataTable.isDataTable(id)) {
            var table = $(id).addClass('nowrap').DataTable({
                "aaData": data,
                processing: true,
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                columns: [
                    { data: 'jenis_pengeluaran', name: 'jenis_pengeluaran' },
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
                            url = '/danakeluar/kategori/' + row['id_jenis_pengeluaran'] + "/" + pathTime;
                            return "<a href=" + url + ">" + data + "</a>";
                        }
                    },
                    { targets: 2, data: "jumlah", render: function (data, type, row, meta) { return formatRupiah(data) } },
                    {
                        targets: 3, data: "waktu", render: function (data, type, row, meta) {
                            var mydate = new Date(row['waktu']);
                            mY = monthYear(mydate);
                            return "<a href='/danakeluar/kategori/" + row['id_jenis_pengeluaran'] + "/'>" + data + "</a>";
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

    if ($rootScope.pathname.length == 3) {
        ajaxGet('/kategoriDKR', returnData);
    }
    if ($rootScope.pathname.length == 4) {
        ajaxGet('/kategoriDKR/' + $("#time").val(), returnData);
    }

    $('#time').change(function () {
        time = $("#time").val();
        url = '/kategori/danakeluar/' + time;
        console.log(url);
        window.location = url;
    });

});