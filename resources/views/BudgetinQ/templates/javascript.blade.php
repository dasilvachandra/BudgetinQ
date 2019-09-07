<script src="{{asset('front_end/js/angular/angular.min.js')}}"></script>
<script src="{{asset('front_end/js/angular/angular-route.min.js')}}"></script>
<script src="{{asset('front_end/vendor/jquery/jquery.min.js')}}"></script>
<script src="{{asset('front_end/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

<!-- Core plugin JavaScript-->
<script src="{{asset('front_end/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

<!-- Custom scripts for all pages-->
<script src="{{asset('front_end/js/sb-admin-2.min.js')}}"></script>

<!-- Page level plugins -->
<script src="{{asset('front_end/vendor/chart.js/Chart.min.js')}}"></script>

<!-- Page level custom scripts -->
<script src="{{asset('front_end/js/fungsi.js')}}"></script>
@yield('javascript')

<script src="{{asset('front_end/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js')}}"></script>

<script>
    $('#selectMonth').datepicker({
        autoclose: true,
        container: '#selectMonth',
        format: "MM, yyyy",
        viewMode: "months",
        minViewMode: "months",
        startDate: new Date("2017-01-01"),
        endDate: new Date(),
        onClose: function (dateText, inst) {
            $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
        }
    })

    $('#time').change(function () {
        time = $("#time").val();
        var url = window.location.href;
        var host = new URL(url).host;
        var pathname = new URL(url).pathname.split("/")[1];
        window.location = "/" + pathname + "/" + time;
        // returnDataHome($("#time").val(),"/data");
    });
</script>

<script src="{{asset('front_end/js/angular/router.js')}}"></script>