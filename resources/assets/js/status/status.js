$(document).ready(function () {
    'use strict';
    
    $(document).on('submit', '#addNewStatusForm', function (event) {
        event.preventDefault();
        var loadingButton = jQuery(this).find('#btnSave');
        loadingButton.button('loading');
        $.ajax({
            url: route('status.store'),
            type: 'POST',
            data: $(this).serialize(),
            success: function (result) {
                if (result.success) {
                    displaySuccessMessage(result.message);
                    $('#addStatusModal').modal('hide');
                    window.livewire.emit('refresh');
                }
            },
            error: function (result) {
                printErrorMessage('#validationErrorsBox', result);
            },
            complete: function () {
                loadingButton.button('reset');
                window.livewire.emit('refresh');
            },
        });
    });

    $(document).on('submit', '#editForm', function (event) {
        event.preventDefault();
        var loadingButton = jQuery(this).find('#btnEditSave');
        loadingButton.button('loading');
        var id = $('#statusId').val();
        $.ajax({
            url: route('status.update', id),
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
                window.livewire.emit('refresh');
            },
        });
    });

    $('#addStatusModal').on('hidden.bs.modal', function () {
        resetModalForm('#addNewStatusForm', '#validationErrorsBox');
    });

    $('#EditModal').on('hidden.bs.modal', function () {
        resetModalForm('#editForm', '#editValidationErrorsBox');
    });

    window.renderData = function (id) {
        $.ajax({
            url: route('status.edit',id),
            type: 'GET',
            success: function (result) {
                if (result.success) {
                    let element = document.createElement('textarea');
                    element.innerHTML = result.data.name;
                    $('#statusId').val(result.data.id);
                    $('#statusName').val(element.value);
                    $('#orderNum').val(result.data.order);
                    $('#EditModal').appendTo('body').modal('show');
                    if (result.data.status === 0 || result.data.status ===
                        1) {
                        $('.edit_name').hide();
                    } else {
                        $('.edit_name').show();
                    }
                }
            },
            error: function (result) {
                manageAjaxErrors(result);
            },
        });
    };

    $(document).on('click', '.edit-btn', function (event) {
        let statusId = $(event.currentTarget).attr('data-id');
        renderData(statusId);

    });

    $(document).on('click', '.delete-btn', function (event) {
        let statusId = $(event.currentTarget).attr('data-id');
        deleteItem(route('status.destroy',statusId), '#status_table', 'Status',
            'location.reload()');
    });

    $(document).on('click', '.addNewStatus', function () {
        $('#addStatusModal').appendTo('body').modal('show');
    });

    $(document).on('click', '#status_modal', function () {
        $.ajax({
            url: 'order',
            type: 'get',
            success: function (result) {
                $('#order').val(result.data.order + 1);
            },
        });
    });

    $('.modal').on('show.bs.modal', function () {
        $(this).appendTo('body');
    });
});
