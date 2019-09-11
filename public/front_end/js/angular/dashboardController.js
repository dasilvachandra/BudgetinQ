app.controller('dashboardController', function ($scope, $rootScope, $routeParams) {
    // console.log($rootScope.pathname);
    $('#time').change(function () {
        time = $("#time").val();
        url = '/dashboard/' + time;
        window.location = url;
    });

});