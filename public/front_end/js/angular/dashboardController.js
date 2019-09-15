app.controller('dashboardController', function ($scope, $rootScope, $routeParams) {
    // console.log($rootScope.pathname);
    $scope.category = function (data, gcID) {
        a = data.filter(function (b) {
            return b.group_category_id == gcID;
        });
        // console.log(a);
        return a;
    };
    $('#time').change(function () {
        time = $("#time").val();
        url = '/dashboard/' + time;
        window.location = url;
    });
    function returnData(data) {
        // console.log(data);
        $scope.cPengeluaran = data['cPengeluaran'];
        $scope.gcPengeluaran = data['gcPengeluaran'];
        $scope.mGC = '1';
        $scope.$apply();
    }
    data = {
        "_token": $('meta[name="csrf-token"]').attr('content'),
        "time": $("#time").val()
    };
    ajaxPost('/dashboardResponse', data, returnData);


    function returnDataChartPie(data) {
        initDonutChart(data['dataDonut'])
        console.log(data['dataDonut']);
        var i = 0;
        labels = [];
        persen = [];
        color = [];
        while (i < data['list_pengeluaran'].length) {
            persen.push(data['list_pengeluaran'][i].persen);
            labels.push(data['list_pengeluaran'][i].jenis_pengeluaran);
            color.push(data['list_pengeluaran'][i].color);
            i++;
        }
        console.log(labels.length);

        // chartPie(data);
    }

    data = {
        "_token": $('meta[name="csrf-token"]').attr('content'),
        "time": $("#time").val(),
        "group_category_id": '1'
    };
    ajaxPost('/chartPie', data, returnDataChartPie);


    $scope.onChangeChartPie = function (value) {
        // console.log(value);
        data = {
            "_token": $('meta[name="csrf-token"]').attr('content'),
            "time": $("#time").val(),
            "group_category_id": value
        };
        ajaxPost('/chartPie', data, returnDataChartPie);
    }

});



// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// Pie Chart Example
function chartPie(data) {
    $("#myPieChart").empty();
    var ctx = document.getElementById("myPieChart");
    var myPieChart = new Chart(ctx, {
        type: 'doughnut',
        data: data,
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: false
            },
            cutoutPercentage: 80,
        },
    });

}

function initDonutChart(data) {
    $("#donut_chart").empty();
    d = JSON.parse(data);
    i = 0; checknull = 0; colors = [];
    while (i < d.length) {
        value = d[i]['value'];
        checknull = checknull + value;
        colors.push(d[i]['colors']);
        i = i + 1;
    }
    if (checknull != 0) {
        Morris.Donut({
            element: 'donut_chart',
            data: JSON.parse(data),

            colors: colors,
            formatter: function (y) {
                return number_format(y);
            }
        });
    } else {
        var data = []; // <-- your empty set
        Morris.Donut({
            element: 'donut_chart',
            data: data.length ? data : [{ label: "No Data", value: 100 }],
            formatter: function (y) {
                return y + "%";
            }
        });
    }
}