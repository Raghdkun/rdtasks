$(document).ready(function () {
    'use strict';
    let tableId = '#activity_type'
    $(tableId).DataTable({
        processing: true,
        serverSide: true,
        'order': [[0, 'asc']],
        ajax: {
            url: route('activity-types.index'),
        },
        columnDefs: [
            {
                'targets': [1],
                'orderable': false,
                'className': 'text-center',
                'width': '5%',
            },
        ],
        'fnInitComplete': function () {
        },
        columns: [
            {
                data: 'name', name: 'name',
            },
            {
                data: function (row) {
                    return '<a title="Edit" class="btn action-btn btn-primary btn-sm edit-btn mr-1" data-id="' +
                        row.id + '">' +
                        '<i class="cui-pencil action-icon"></i>' + '</a>' +
                        '<a title="Delete" class="btn action-btn btn-danger btn-sm delete-btn" data-id="' +
                        row.id + '">' +
                        '<i class="cui-trash action-icon" ></i></a>';
                }, name: 'id',
            },
        ],
    })

    $(document).on('submit', '#addNewForm', function (event) {
        event.preventDefault();
        var loadingButton = jQuery(this).find('#btnSave');
        loadingButton.button('loading');
        $.ajax({
            url: route('activity-types.store'),
            type: 'POST',
            data: $(this).serialize(),
            success: function (result) {
                if (result.success) {
                    displaySuccessMessage(result.message);
                    $('#AddModal').modal('hide');
                    $('#activity_type').DataTable().ajax.reload(null, false);
                    window.livewire.emit('refresh');
                }
            },
            error: function (result) {
                printErrorMessage('#validationErrorsBox', result);
            },
            complete: function () {
                loadingButton.button('reset');
            },
        })
    })

    $(document).on('submit', '#editForm', function (event) {
        event.preventDefault();
        var loadingButton = jQuery(this).find('#btnEditSave');
        loadingButton.button('loading');
        var id = $('#activityTypeId').val();
        $.ajax({
            url: route('activity-types.update',id),
            type: 'put',
            data: $(this).serialize(),
            success: function (result) {
                if (result.success) {
                    displaySuccessMessage(result.message);
                    $('#EditModal').modal('hide');
                    $('#activity_type').DataTable().ajax.reload(null, false);
                    window.livewire.emit('refresh');
                }
            },
            error: function (result) {
                manageAjaxErrors(result);
            },
            complete: function () {
                loadingButton.button('reset');
            },
        })
    })

    $(document).on('click', '.addNewActivityType', function (event) {
        $('#AddModal').appendTo('body').modal('show');
    });

    $('#AddModal').on('hidden.bs.modal', function () {
        resetModalForm('#addNewForm', '#validationErrorsBox');
    });

    $('#EditModal').on('hidden.bs.modal', function () {
        resetModalForm('#editForm', '#editValidationErrorsBox');
    });

    window.renderData = function (id) {
        $.ajax({
            url: route('activity-types.edit',id),
            type: 'GET',
            success: function (result) {
                if (result.success) {
                    $('#activityTypeId').val(result.data.id)
                    let element = document.createElement('textarea');
                    element.innerHTML = result.data.name;
                    $('#activityType').val(element.value);
                    $('#EditModal').appendTo('body').modal('show');
                }
            },
            error: function (result) {
                manageAjaxErrors(result)
            },
        })
    }
    
    $(document).on('click', '.edit-btn', function (event) {
        let activityId = $(event.currentTarget).attr('data-id');
        renderData(activityId);
    });
    
    $(document).on('click', '.delete-btn', function (event) {
        
        let activityId = $(event.currentTarget).attr('data-id');
        let runningTaskId = localStorage.getItem('currentActivityId');
        
        if (activityId == runningTaskId) {
            displayErrorMessage('This Activity Type use in tracker');
            return false;
        }
        
        deleteItem(route('activity-types.destroy',activityId), '#activity_type', 'Activity Type',
            'location.reload()');
    });

    $('.modal').on('show.bs.modal', function () {
        $(this).appendTo('body');
    });
});
