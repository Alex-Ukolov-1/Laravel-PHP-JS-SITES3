Modal = {
    showModal: function (selector, callbackBefore, callbackAfter) {
        callbackBefore();
        $(selector).modal('show');
        $(selector).on('hidden.bs.modal', function (e) {
            callbackAfter();
        });
    }
}

module.exports = Modal;
