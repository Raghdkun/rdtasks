'use strict';

const pickr = Pickr.create({
    el: '.color-wrapper',
    theme: 'nano', // or 'monolith', or 'nano'
    closeWithKey: 'Enter',
    autoReposition: true,
    defaultRepresentation: 'HEX',
    position: 'bottom-end',
    swatches: [
        'rgba(244, 67, 54, 1)',
        'rgba(233, 30, 99, 1)',
        'rgba(156, 39, 176, 1)',
        'rgba(103, 58, 183, 1)',
        'rgba(63, 81, 181, 1)',
        'rgba(33, 150, 243, 1)',
        'rgba(3, 169, 244, 1)',
        'rgba(0, 188, 212, 1)',
        'rgba(0, 150, 136, 1)',
        'rgba(76, 175, 80, 1)',
        'rgba(139, 195, 74, 1)',
        'rgba(205, 220, 57, 1)',
        'rgba(255, 235, 59, 1)',
        'rgba(255, 193, 7, 1)',
    ],

    components: {
        // Main components
        preview: true,
        hue: true,

        // Input / output Options
        interaction: {
            input: true,
            clear: false,
            save: false,
        },
    },
});

const editPickr = Pickr.create({
    el: '.color-wrapper',
    theme: 'nano', // or 'monolith', or 'nano'
    closeWithKey: 'Enter',
    autoReposition: true,
    defaultRepresentation: 'HEX',
    position: 'bottom-end',
    swatches: [
        'rgba(244, 67, 54, 1)',
        'rgba(233, 30, 99, 1)',
        'rgba(156, 39, 176, 1)',
        'rgba(103, 58, 183, 1)',
        'rgba(63, 81, 181, 1)',
        'rgba(33, 150, 243, 1)',
        'rgba(3, 169, 244, 1)',
        'rgba(0, 188, 212, 1)',
        'rgba(0, 150, 136, 1)',
        'rgba(76, 175, 80, 1)',
        'rgba(139, 195, 74, 1)',
        'rgba(205, 220, 57, 1)',
        'rgba(255, 235, 59, 1)',
        'rgba(255, 193, 7, 1)',
    ],

    components: {
        // Main components
        preview: true,
        hue: true,

        // Input / output Options
        interaction: {
            input: true,
            clear: false,
            save: false,
        },
    },
});

pickr.on('change', function () {
    const color = pickr.getColor().toHEXA().toString();

    if (wc_hex_is_light(color)) {
        $('#validationErrorsBox').text('');
        $('#validationErrorsBox').show().html('');
        $('#validationErrorsBox').text('Pick a different color');
        setTimeout(function () {
            $('#validationErrorsBox').slideUp();
        }, 5000);
        $(':input[id="btnSave"]').prop('disabled', true);
        return;
    }
    $(':input[id="btnSave"]').prop('disabled', false);
    pickr.setColor(color);
    $('#color').val(color);
});

editPickr.on('change', function () {
    const color = editPickr.getColor().toHEXA().toString();
    if (wc_hex_is_light(color)) {
        $('#editValidationErrorsBox').text('');
        $('#editValidationErrorsBox').show().html('');
        $('#editValidationErrorsBox').text('Pick a different color');
        setTimeout(function () {
            $('#editValidationErrorsBox').slideUp();
        }, 5000);
        $(':input[id="btnEditSave"]').prop('disabled', true);
        return;
    }
    $(':input[id="btnEditSave"]').prop('disabled', false);
    editPickr.setColor(color);
    $('#edit_color').val(color);
});
$(document).ready(function () {

    $('#departments-table').DataTable({
        processing: true,
        serverSide: true,
        'order': [[0, 'asc']],
        ajax: {
            url: route('departments.index'),
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
                        '<a title="Delete" class="btn action-btn btn-danger btn-sm delete-btn"  data-id="' +
                        row.id + '">' +
                        '<i class="cui-trash action-icon"></i></a>';
                }, name: 'id',
            },
        ],
    });

    $(document).on('click', '.addNewDepartment', function (event) {
        $('.pcr-button').
            css({ 'color': '#3F51B5', 'border': '1px solid grey' });
        $('#AddModal').appendTo('body').modal('show');
    });

    $('#description,#editDescription').summernote({
        placeholder: 'Add Department description...',
        minHeight: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['para', ['paragraph']]],
    });

    let picked = false;

    $(document).on('click', '#color', function () {
        picked = true;
    });

    $(document).on('submit', '#addNewForm', function (event) {
        event.preventDefault();
        let $description = $('<div />').html($('#description').summernote('code'));
        let empty = $description.text().trim().replace(/ \r\n\t/g, '') === '';
        let loadingButton = jQuery(this).find('#btnSave');
        loadingButton.button('loading');
        if ($('#color').val() == '') {
            displayErrorMessage('Please select your color.');
            loadingButton.button('reset');
            return false;
        }
        if($('#description').summernote('isEmpty')){
            $('#description').val('');
        } else if (empty){
            displayErrorMessage('Description field is not contain only white space');
            loadingButton.button('reset');
            return false;
        }
        let form = $(this);
        let formdata = $(this).serializeArray();

        $.ajax({
            url: route('departments.store'),
            type: 'POST',
            data: formdata,
            success: function (result) {
                if (result.success) {
                    displaySuccessMessage(result.message);
                    $('#AddModal').modal('hide');
                    $('#departments-table').
                        DataTable().
                        ajax.
                        reload(null, false);
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
        let $description = $('<div />').html($('#editDescription').summernote('code'));
        let empty = $description.text().trim().replace(/ \r\n\t/g, '') === '';
        let form = $(this);
        let loadingButton = jQuery(this).find('#btnEditSave');
        loadingButton.button('loading');
        if ($('#editDescription').summernote('isEmpty')) {
            $('#editDescription').val('');
        }else if (empty){
            displayErrorMessage('Description field is not contain only white space');
            loadingButton.button('reset');
            return false;
        }
        let formdata = $(this).serializeArray();
        let id = $('#departmentId').val();
        $.ajax({
            url: route('departments-update',id),
            type: 'post',
            data: formdata,
            success: function (result) {
                if (result.success) {
                    displaySuccessMessage(result.message);
                    $('#EditModal').modal('hide');
                    $('#departments-table').
                        DataTable().
                        ajax.
                        reload(null, false);
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
        $('#description').summernote('code', '');
        pickr.setColor('#000000');
        pickr.hide();
        resetModalForm('#addNewForm', '#validationErrorsBox');
    });

    $('#EditModal').on('hidden.bs.modal', function () {
        $('#editDescription').summernote('code', '');
        editPickr.hide();
        resetModalForm('#editForm', '#editValidationErrorsBox');
    });

    window.renderData = function (id) {
        $.ajax({
            url: route('departments.edit',id),
            type: 'GET',
            success: function (result) {
                if (result.success) {
                    let department = result.data;
                    let element = document.createElement('textarea');
                    element.innerHTML = department.name;
                    $('#departmentId').val(department.id);
                    $('#edit_name').val(element.value);
                    $('#editDescription').
                        summernote('code', department.description);
                    editPickr.setColor(department.color);
                    $('#edit_email').val(department.email);
                    $('#edit_website').val(department.website);
                    $('#EditModal').appendTo('body').modal('show');
                }
            },
            error: function (result) {
                manageAjaxErrors(result);
            },
        });
    };

    $(document).on('click', '.edit-btn', function (event) {
        let departmentId = $(event.currentTarget).attr('data-id');
        renderData(departmentId);
    });

    $(document).on('click', '.delete-btn', function (event) {
        let departmentId = $(event.currentTarget).attr('data-id');
        deleteItem(route('departments.destroy',departmentId), '#departments-table',
            'Department');
    });

    $(document).on('click', '.show-btn', function (event) {
        let departmentId = $(event.currentTarget).attr('data-id');
        $.ajax({
            url: route('departments.show',departmentId),
            type: 'GET',
            beforeSend: function () {
                startLoader();
            },
            success: function (result) {
                if (result.success) {
                    $('#showName').html('');
                    $('#showDescription').html('');
                    $('#showName').append(result.data.name);
                    let element = document.createElement('textarea');
                    element.innerHTML = (!isEmpty(result.data.description))
                        ? result.data.description
                        : 'N/A';
                    $('#showDescription').append(element.value);
                    $('#showModal').appendTo('body').modal('show');
                }
            },
            error: function (result) {
                displayErrorMessage(result.responseJSON.message);
            },
            complete: function () {
                stopLoader();
            },
        });
    });
});

