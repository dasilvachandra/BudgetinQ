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