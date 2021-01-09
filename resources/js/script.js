
function confirmDelete(el, message, title) {
    bootbox.confirm({
        title: title,
        message: message,
        buttons: {
            confirm: {
                label: 'Delete',
                className: 'btn-danger'
            },
        },
        callback: function (result) {
            if (result) {
                if (typeof el === 'string' ) {
                    window.location.href = el;
                } else {
                    el.submit();
                }
            }
        }
    });
    return false;
}
