app.controller('kategoriDKController', function ($scope, $rootScope, $routeParams) {
    console.log("KategoriDKController");
    function returnData(data) {
        console.log(data);
        tableJenisPengeluaran(data['list_jenis_pengeluaran']);
    }

    // data = {
    //     "_token": $('meta[name="csrf-token"]').attr('content'),
    //     "time": $("#time").val()
    // };
    ajaxGet('/kategoriDKR', returnData);

    // TABLE
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
                            url = "<a href='/danakeluar/" + mY + "/" + dd + "'>" + data + "</a>";
                            return url;
                        }
                    },
                    { targets: 2, data: "jumlah", render: function (data, type, row, meta) { return formatRupiah(data) } },
                    {
                        targets: 3, data: "waktu", render: function (data, type, row, meta) {
                            var mydate = new Date(row['waktu']);
                            mY = monthYear(mydate);
                            return "<a href='/kategori/danakeluar/" + row['group_category_id'] + "/" + row['id_jenis_pengeluaran'] + "/ " + mY + "/'>" + data + "</a>";
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
});