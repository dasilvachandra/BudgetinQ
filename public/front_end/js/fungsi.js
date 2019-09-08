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

function datePickerForm(by){
    $(by).bootstrapMaterialDatePicker({
        format: 'DD MMMM, YYYY',
        clearButton: true,
        weekStart: 1,
        time: false,
        minDate: new Date("2017-12-12"),
        maxDate: new Date()
    });
}

function input_rupiah(id){
    nilaiInputUang = document.getElementById(id).value;
    document.getElementById(id).value = ""+fRupiah(nilaiInputUang);
    var dengan_rupiah = document.getElementById(id);
    dengan_rupiah.addEventListener('keyup', function(e)
    {
        dengan_rupiah.value = fRupiah(this.value, 'Rp ');
    });

    function fRupiah(angka, prefix)
    {
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