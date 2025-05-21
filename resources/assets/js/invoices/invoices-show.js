$(document).ready(function () {
    'use strict';

    $(document).on('click', '.mark-as-paid', function (event) {
        let loadingButton = jQuery(this);
        loadingButton.button('loading');
        let invoiceId = $(event.currentTarget).attr('data-id');
        $.ajax({
            url: route('invoices-update-status', invoiceId),
            method: 'post',
            cache: false,
            success: function (result) {
                if (result.success) {
                    displaySuccessMessage(result.message);
                    location.reload();
                }
            },
            error: function (result) {
                manageAjaxErrors(result);
            },
            complete: function () {
                loadingButton.button('reset');
            },
        });
    });
});
