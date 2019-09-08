app.controller('danamasukController', function ($scope, $rootScope, $routeParams) {
    $scope.category = function (data, gcID) {
        a = data.filter(function (b) {
            return b.group_category_id == gcID;
        });
        return a;
    };
    function returnData(data) {
        $scope.cPemasukkan = data['cPendapatan'];
        $scope.gcPemasukkan = data['gcPendapatan'];
        tablePendapatan(data['list_pemasukkan']);
        $scope.$apply();
    }

    data = {
        "_token": $('meta[name="csrf-token"]').attr('content'),
        "time": $("#time").val()
    };

    ajaxPost('/dataGC', data, returnData);
    function danamasukRefresh(data) {
        tablePendapatan(data['list_pemasukkan']);
        document.getElementById("closeModPengCreate").click();
        showNotification('bg-green', "Succsessfully", "bottom", "right", "animated rotateInDownRight", "animated rotateOutDownRight");
    }
    $scope.danamasukStore = function (url, formID) {
        ajaxPost(url, $(formID).serialize(), danamasukRefresh);
    };
    // TABLE
    function tablePendapatan(data) {
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