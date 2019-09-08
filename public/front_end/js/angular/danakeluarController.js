app.controller('danakeluarController', function ($scope, $rootScope, $routeParams) {
    $scope.category = function (data, gcID) {
        a = data.filter(function (b) {
            return b.group_category_id == gcID;
        });
        return a;
    };
    // FIRST
    function returnData(data) {

        $scope.cPengeluaran = data['cPengeluaran'];
        $scope.gcPengeluaran = data['gcPengeluaran'];
        $scope.$apply();
    }

    data = {
        "_token": $('meta[name="csrf-token"]').attr('content'),
        "time": $("#time").val()
    };

    ajaxPost('/dataGC', data, returnData);

    //REFRESH 
    function danakeluarRefresh(data) {
        console.log(data);
    }

    // CREATE
    $scope.danakeluarStore = function (url, formID) {
        ajaxPost(url, $(formID).serialize(), danakeluarRefresh);
    };

});