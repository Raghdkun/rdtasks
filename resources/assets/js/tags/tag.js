$(document).ready(function () {
    'use strict';

    $('#tags_table').DataTable({
        processing: true,
        serverSide: true,
        'order': [[0, 'asc']],
        ajax: {
            url: route('tags.index'),
        },
        columnDefs: [
            {
                'targets': [1],
                'orderable': false,
                'className': 'text-center',
                'width': '5%',
            },
        ],
        columns: [
            {
                data: 'name',
                name: 'name',
            },
            {
                data: function (row) {
                    return '<a title="Edit" class="btn action-btn btn-primary btn-sm edit-btn mr-1" data-id="' +
                        row.id + '">' +
                        '<i class="cui-pencil action-icon"></i>' + '</a>' +
                        '<a title="Delete" class="btn action-btn btn-danger btn-sm delete-btn" data-id="' +
                        row.id + '">' +
                        '<i class="cui-trash action-icon"></i></a>';
                }, name: 'id',
            },
        ],
    });

    $(document).on('submit', '#addNewForm', function (event) {
        event.preventDefault();
        var loadingButton = jQuery(this).find('#btnSave');
        loadingButton.button('loading');
        $.ajax({
            url: route('tags.store'),
            type: 'POST',
            data: $(this).serialize(),
            success: function (result) {
                if (result.success) {
                    displaySuccessMessage(result.message);
                    $('#AddModal').modal('hide');
                    $('#tags_table').DataTable().ajax.reload(null, false);
                    window.livewire.emit('refresh');
                }
            },
            error: function (result) {
                printErrorMessage('#validationErrorsBox', result);
            },
            complete: function () {
                loadingButton.button('reset');
            },
        });
    });

    $(document).on('submit', '#editForm', function (event) {
        event.preventDefault();
        var loadingButton = jQuery(this).find('#btnEditSave');
        loadingButton.button('loading');
        var id = $('#tagId').val();
        $.ajax({
            url: route('tags.update', id),
            type: 'put',
            data: $(this).serialize(),
            success: function (result) {
                if (result.success) {
                    displaySuccessMessage(result.message);
                    $('#EditModal').modal('hide');
                    $('#tags_table').DataTable().ajax.reload(null, false);
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

    $('#AddModal').on('hidden.bs.modal', function () {
        $('#tagHeader').html(newTag);
        resetModalForm('#addNewForm', '#validationErrorsBox');
    });

    $('#EditModal').on('hidden.bs.modal', function () {
        resetModalForm('#editForm', '#editValidationErrorsBox');
    });

    window.renderData = function (id) {
        $.ajax({
            url: route('tags.edit',id),
            type: 'GET',
            success: function (result) {
                if (result.success) {
                    let element = document.createElement('textarea');
                    element.innerHTML = result.data.name;
                    $('#tagId').val(result.data.id);
                    $('#tagName').val(element.value);
                    $('#EditModal').appendTo('body').modal('show');

                }
            },
            error: function (result) {
                manageAjaxErrors(result);
            },
        });
    };

    window.setBulkTags = function () {
        $('#isBulkTags').val(true);
        $('#tagHeader').html(addBulkTag);
    };

    $(document).on('click', '.edit-btn', function (event) {
        let tagId = $(event.currentTarget).attr('data-id');
        renderData(tagId);

    });

    $(document).on('click', '.delete-btn', function (event) {
        let tagId = $(event.currentTarget).attr('data-id');
        deleteItem(route('tags.destroy',tagId), '#tags_table', 'Tag', 'location.reload()');
    });

    $(document).on('click', '.addBulkTags', function () {
        $('#AddModal').appendTo('body').modal('show');
    });

    $(document).on('click', '.addNewTag', function () {
        $('#AddModal').appendTo('body').modal('show');
    });

    $('.modal').on('show.bs.modal', function () {
        $(this).appendTo('body');
    });
});
