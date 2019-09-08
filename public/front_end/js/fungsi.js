function ajaxPost(url, data, callback) {
    $.post(url, data, function (response) {
        callback(response);
    }).fail(function (err) {
        var errors = $.parseJSON(err.responseText)['errors'];
        // console.log(err[]);
        for (let elem in errors) {
            error_messages = errors[elem];
            showNotification('bg-red', error_messages, "bottom", "right", "animated rotateInDownRight", "animated rotateOutDownRight");
        }
    });
}

function ajaxGet(url, callback) {
    $.get(url, function (response) {
        callback(response);
    }).fail(function (err) {
        var errors = $.parseJSON(err.responseText)['errors'];
        for (let elem in errors) {
            error_messages = errors[elem];
            showNotification('bg-red', error_messages, "bottom", "right", "animated rotateInDownRight", "animated rotateOutDownRight");
        }
    });
}

function datePickerForm(by) {
    $(by).bootstrapMaterialDatePicker({
        format: 'DD MMMM, YYYY',
        clearButton: true,
        weekStart: 1,
        time: false,
        minDate: new Date("2017-12-12"),
        maxDate: new Date()
    });
}

function input_rupiah(id) {
    nilaiInputUang = document.getElementById(id).value;
    document.getElementById(id).value = "" + fRupiah(nilaiInputUang);
    var dengan_rupiah = document.getElementById(id);
    dengan_rupiah.addEventListener('keyup', function (e) {
        dengan_rupiah.value = fRupiah(this.value, 'Rp ');
    });

    function fRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split('.'),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        rupiah = split[1] != undefined ? rupiah + '.' + split[1] : rupiah;
        return prefix == "" ? ' ' + rupiah : (rupiah ? '' + rupiah : '');
    }
}

function showNotification(colorName, text, placementFrom, placementAlign, animateEnter, animateExit) {
    if (colorName === null || colorName === '') { colorName = 'bg-black'; }
    if (text === null || text === '') { text = 'Turning standard Bootstrap alerts'; }
    if (animateEnter === null || animateEnter === '') { animateEnter = 'animated fadeInDown'; }
    if (animateExit === null || animateExit === '') { animateExit = 'animated fadeOutUp'; }
    var allowDismiss = true;
    var pesan = "<div class='alert alert-danger bootstrap-notify-container'><button type='button' aria-hidden='true' class='close' data-notify='dismiss'>×</button> <strong>Success!</strong> Indicates a successful or positive action.</div>";

    $.notify({
        message: text
    },
        {
            type: colorName,
            allow_dismiss: allowDismiss,
            newest_on_top: true,
            timer: 1000,
            placement: {
                from: placementFrom,
                align: placementAlign
            },
            animate: {
                enter: animateEnter,
                exit: animateExit
            },
            template: '<div data-notify="container" class=" alert-danger bootstrap-notify-container alert alert-dismissible {0} ' + (allowDismiss ? "p-r-35" : "") + '" role="alert">' +
                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                '<span data-notify="icon"></span> ' +
                '<span data-notify="title">{1}</span> ' +
                '<span data-notify="message">{2}</span>' +
                '<div class="progress" data-notify="progressbar">' +
                '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
                '</div>' +
                '<a href="{3}" target="{4}" data-notify="url"></a>' +
                '</div>'
        });
}

function selectCell(id) {
    var table = $(id).DataTable();
    $(id + ' tbody').on('click', 'tr', function () {
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
        }
        else {
            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    });
}

function formatRupiah(angka) {
    var rupiah = '';
    var angkarev = angka.toString().split('').reverse().join('');
    for (var i = 0; i < angkarev.length; i++) if (i % 3 == 0) rupiah += angkarev.substr(i, 3) + ',';
    return '' + rupiah.split('', rupiah.length - 1).reverse().join('');
}