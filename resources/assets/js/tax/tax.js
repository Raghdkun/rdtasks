$(document).ready(function () {
    'use strict';

    $(document).on('submit', '#addNewForm', function (e) {
        e.preventDefault();
        var loadingButton = jQuery(this).find('#btnSave');
        loadingButton.button('loading');
        $.ajax({
            url: route('taxes.index'),
            type: 'POST',
            data: $(this).serialize(),
            success: function (result) {
                if (result.success) {
                    displaySuccessMessage(result.message);
                    $('#AddModal').modal('hide');
                    window.livewire.emit('refresh');
                }
            },
            error: function (result) {
                printErrorMessage('#taxValidationErrorsBox', result);
            },
            complete: function () {
                loadingButton.button('reset');
            },
        });
    });

    $(document).on('click', '.edit-btn', function (event) {
        let taxId = $(event.currentTarget).attr('data-id');
        renderData(taxId);
    });

    window.renderData = function (id) {
        $.ajax({
            url: route('taxes.edit',id),
            type: 'GET',
            success: function (result) {
                if (result.success) {
                    let element = document.createElement('textarea');
                    element.innerHTML = result.data.name;
                    $('#taxId').val(result.data.id);
                    $('#editName').val(element.value);
                    $('#editTax').val(result.data.tax);
                    $('#EditModal').appendTo('body').modal('show');
                }
            },
            error: function (result) {
                manageAjaxErrors(result);
            },
        });
    };

    $(document).on('submit', '#editForm', function (event) {
        event.preventDefault();
        var loadingButton = jQuery(this).find('#btnEditSave');
        loadingButton.button('loading');
        var id = $('#taxId').val();
        $.ajax({
            url: route('taxes.update',id),
            type: 'put',
            data: $(this).serialize(),
            success: function (result) {
                if (result.success) {
                    displaySuccessMessage(result.message);
                    $('#EditModal').modal('hide');
                    window.livewire.emit('refresh');
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

    $(document).on('click', '.delete-btn', function (event) {
        let taxId = $(event.currentTarget).attr('data-id');
        deleteItem(route('taxes.destroy',taxId), '#taxRatesTable', 'Tax');
    });

    $('#AddModal').on('hidden.bs.modal', function () {
        resetModalForm('#addNewForm', '#taxValidationErrorsBox');
    });

    $('#EditModal').on('hidden.bs.modal', function () {
        resetModalForm('#editForm', '#editValidationErrorsBox');
    });

    $(document).on('keyup', '.tax', function () {
        $(this).
            val($(this).
                val().
                replace(/[^0-9.]/g, '').
                replace(/(\..*)\./g, '$1'));
    });
});
