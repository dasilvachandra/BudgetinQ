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

    data = {
        "_token": $('meta[name="csrf-token"]').attr('content'),
        "time": $("#time").val()
    };

    ajaxPost('/dataGC', data, returnData);
    function danakeluarRefresh(data) {
    }
    $scope.danakeluarStore = function (url, formID) {
        ajaxPost(url, $(formID).serialize(), danakeluarRefresh);
    };
    // TABLE
    function tablePengeluaran(data) {
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
                    { targets: 2, data: "price", render: function (data, type, row, meta) { return formatRupiah(data) } },
                    { width: 100, targets: 0 },
                    { width: 100, targets: 1 }
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