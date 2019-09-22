
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

<script src="{{asset('front_end/js/moment.js')}}"></script>
<!-- datepicker -->
<script src="{{asset('front_end/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js')}}"></script>
<script
    src="{{asset('front_end/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js')}}"></script>

    <!-- Morris Plugin Js -->
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>


<script>
    $(document).ready(function () {
        setTimeout(function () { $('.spinner-grow').fadeOut(); }, 100);
        totalTimeLoad = Date.now() - timerStart;
        console.log("Time until DOMready: ", Date.now() - timerStart);
        $('.half').removeClass('d-none').addClass('d-block');
    });

// PAGE LOAD
    var pageStatus = null;
    var progress = null;
    var animationInterval = 33;

    window.document.addEventListener("readystatechange", function () {
        if (document.readyState == "complete") {
            pageStatus = "complete";
        }
    }, false);

    function updateProgress() {
        // console.log(progress);
        // $('.load-page').css({ 'width': progress + '%' });

        if (pageStatus == "complete") {
            $('.load-page').css({ 'width':  '100%' });
            setTimeout(function () {
                // $('.load-page').css({ 'width':  '0%' });
                $('.load-page-master').addClass('d-none');
                // document.getElementById("pageLoader").style.display = "none";
            }, 700);
        }
        else {
            if (progress == null) {
                progress = 1;
            }

            progress = progress + 1;
            if (progress >= 0 && progress <= 30) {
                animationInterval += 1;

                $('.load-page').css({ 'width': progress + '%' });
            }
            else if (progress > 30 && progress <= 60) {
                animationInterval += 2;
                console.log(progress);
                // $('.progress').css({ 'width': progress + '%' });

                $('.load-page').css({ 'width': progress + '%' });
            }
            else if (progress > 60 && progress <= 80) {
                animationInterval += 3;
                $('.load-page').css({ 'width': progress + '%' });
            }
            else if (progress > 80 && progress <= 90) {
                animationInterval += 4;
                $('.load-page').css({ 'width': progress + '%' });
            }
            else if (progress > 90 && progress <= 95) {
                animationInterval += 80;
                $('.load-page').css({ 'width': progress + '%' });
            }
            else if (progress > 95 && progress <= 99) {
                animationInterval += 150;
                $('.load-page').css({ 'width': progress + '%' });
            }
            else if (progress >= 100) {
                $('.load-page').css({ 'width': progress + '%' });
            }
            setTimeout(updateProgress, animationInterval);
        }
    }

    var intervalObject_1 = setInterval(function () {
        var element = document.querySelector("body");

        if (element != undefined) {
            clearInterval(intervalObject_1);
            $('.load-page').css({ 'width': progress + '%' });
            updateProgress();
        }
    }, 50);
// END PAGE LOAD









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

    // $('#time').change(function () {
    //     time = $("#time").val();
    //     var url = window.location.href;
    //     var host = new URL(url).host;
    //     var pathname = new URL(url).pathname.split("/")[1];
    //     window.location = "/" + pathname + "/" + time;
    //     // returnDataHome($("#time").val(),"/data");
    // });
    $(document).ready(function () {
        $('#dataTable').DataTable();
    });
    input_rupiah("input_rupiah")

</script>

<script src="{{asset('front_end/js/angular/router.js')}}"></script>

<!-- Nofity -->
<script src="{{asset('front_end/plugins/bootstrap-notify/bootstrap-notify.js')}} "></script>

<!-- DATATABLES -->
<script src="{{asset('front_end/vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('front_end/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>

@yield('javascript')