app.controller('danamasukController', function ($scope, $rootScope, $routeParams) {
    // console.log("danamasukController");
    $scope.category = function (data, gcID) {
        a = data.filter(function (b) {
            return b.group_category_id == gcID;
        });
        return a;
    };
    function returnData(data) {

        $scope.cPendapatan = data['cPendapatan'];
        $scope.gcPendapatan = data['gcPendapatan'];
        $scope.$apply();
    }

    data = {
        "_token": $('meta[name="csrf-token"]').attr('content'),
        "time": $("#time").val()
    };

    ajaxPost('/dataGC', data, returnData);
});