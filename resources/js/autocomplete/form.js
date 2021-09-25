Form = {
    send: function (selector, url, callback) {
        var data = $(selector).serialize();
        this.clear(selector);
        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            success: function(data) {
                if (data.error) {
                    Form.showErrors(selector, data.error);
                } else {
                    if (typeof callback === 'function') callback(data);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == 422) {
                    var errors = JSON.parse(jqXHR.responseText).errors;
                    var errorsText = '<ul>';
                    for (var key in errors) {
                        errorsText = errorsText + '<li>' + errors[key] + '</li>';
                    }
                    errorsText = errorsText + '</ul>';
                    Form.showErrors(selector, errorsText);
                } else {
                    Form.showErrors(selector, errorThrown);
                }
            }
        });
    },
    clear: function (selector) {
        $('.form-error').remove();
        $('.form-success').remove();
    },
    showErrors: function (selector, error) {
        $(selector).prepend('<div class="form-error alert alert-danger" role="alert">' + error + '</div>');
    }
}

module.exports = Form;
