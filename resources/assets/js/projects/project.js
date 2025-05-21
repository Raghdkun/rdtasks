'use strict';

const pickr = Pickr.create({
    el: '.color-wrapper',
    theme: 'nano', // or 'monolith', or 'nano'
    closeWithKey: 'Enter',
    autoReposition: true,
    defaultRepresentation: 'HEX',
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
        $('.editValidationErrorsBox').text('');
        $('.editValidationErrorsBox').show().html('');
        $('.editValidationErrorsBox').text('Pick a different color');
        setTimeout(function () {
            $('.editValidationErrorsBox').slideUp();
        }, 5000);
        $(':input[id="btnEditSave"]').prop('disabled', true);
        return;
    }
    $(':input[id="btnEditSave"]').prop('disabled', false);
    editPickr.setColor(color);
    $('#edit_color').val(color);
});

$(document).ready(function () {
    $('#client_id,#edit_client_id').select2({
        width: canManageClients?'calc(100% - 44px)':'100%',
        placeholder: 'Select Client',
    });
    $('#budget_type,#edit_budget_type,#editStatusProject,#projectStatus').
        select2({
            width: '100%',
        });

    $('#filterClient').select2();
    $('#currency,#editCurrency').select2({
        width: '100%',
        // placeholder: 'Select Currency',
    });
    $('#user_ids,#edit_user_ids').select2({
        width: '100%',
        placeholder: 'Select Users',
    });
    $('#editProjectUser').select2({
        width: '100%',
    });
    $('#department_id,#edit_department_id').select2({
        width: '100%',
        placeholder: 'Select Department',
    });
});
$(document).on('click','.edit-project-assignees',function (e){
    let id = $(this).attr('data-id');
    startLoader();
    $.ajax({
       url: route('projects.edit',id),
       type: 'GET',
       success: function (result)
       {
           if (result.success) {
               let projectId = result.data.project['id'];
               let allUsers = result.data.project.allUsers;
               $.each(allUsers, function( index, value ) {
                   $('#editProjectUser').
                       append($('<option>', { value: value, text: value.name }));
               })
               $('#hdnProjectId').val(projectId);
               let userIds = result.data.users;
               $('#editProjectUser').val(userIds).trigger('change')
               $("#editProjectUser").val(result.data.users).trigger('change');
               stopLoader();
               $('#assignProjectUserModal').appendTo('body').modal('show');
           }
       },
        error: function (error) {
            manageAjaxErrors(error)
        },
    });
});
$(document).on('click', '#btnSaveAssigneesProject', function () {
    var loadingButton = jQuery(this);
    loadingButton.button('loading');
    window.livewire.emit('updateAssigneesProject', $('#editProjectUser').val(),
        $('#hdnProjectId').val());
    $('#assignProjectUserModal').modal('hide');
    displaySuccessMessage('Project assignee updated successfully');
});

$('#assignProjectUserModal').on('hidden.bs.modal', function () {
    $('#editProjectUser').val(null).trigger('change');
    var loadingButton = jQuery('#btnSaveAssigneesProject');
    loadingButton.button('reset');
});

$('#description,#editDescription').summernote({
    placeholder: 'Add Project description...',
    minHeight: 200,
    toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough']],
        ['para', ['paragraph']]],
});

let tbl = $('#projects_table').DataTable({
    processing: true,
    serverSide: true,
    'order': [[0, 'asc']],
    ajax: {
        url: route('projects.index'),
        data: function (data) {
            data.filter_client = $('#filterClient').
                find('option:selected').
                val()
        },
    },
    columnDefs: [
        {
            'targets': [0],
            'className': 'text-center',
            'width': '7%',
        },
        {
            'targets': [3],
            'orderable': false,
            'className': 'text-center',
            'width': '5%',
        },
    ],
    columns: [
        {
            data: 'prefix',
            name: 'prefix',
        },
        {
            data: 'name',
            name: 'name',
        },
        {
            data: 'client.name',
            defaultContent: '',
            name: 'client.name',
        },
        {
            data: function (row) {
                return '<a title="Edit" class="btn action-btn btn-primary btn-sm edit-btn mr-1" data-id="' +
                    row.id + '">' +
                    '<i class="cui-pencil action-icon"></i>' + '</a>' +
                    '<a title="Delete" class="btn action-btn btn-danger btn-sm delete-btn" data-id="' +
                    row.id + '">' +
                    '<i class="cui-trash action-icon" ></i></a>'
            }, name: 'id',
        },
    ],
    'fnInitComplete': function () {
        $(document).on('change', '#filterClient', function () {
            tbl.ajax.reload();
        });
    },
})

var picked = false;

$(document).on('submit', '#color', function () {
    picked = true;
});

$('#AddModal').on('show.bs.modal', function (event) {
    $('.pcr-button').css({ 'color': '#3F51B5', 'border': '1px solid grey' });
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
    let form = $(this);
    let formdata = $(this).serializeArray();
    formdata[formdata.length] = { name: 'color', value: $('#color').val() };
    $.ajax({
        url: route('projects.store'),
        type: 'POST',
        data: formdata,
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#AddModal').modal('hide');
                $('#projects_table').DataTable().ajax.reload(null, false);
                revokerTracker();
                window.livewire.emit('refresh');
            }
        },
        error: function (result) {
            printErrorMessage('#validationErrorsBox', result)
        },
        complete: function () {
            loadingButton.button('reset')
        },
    })
})

$(document).on('submit', '#editFormProject', function (event) {
    event.preventDefault();
    let $description = $('<div />').
        html($('#editDescription').summernote('code'));
    let empty = $description.text().trim().replace(/ \r\n\t/g, '') === '';
    if ($('#editPrice').val() == 0) {
        displayErrorMessage('The budget amount should be a minimum of 1.');
        return false;
    }
    if (removeCommas($('#editPrice').val()).length > 12) {
        displayErrorMessage('Maximum 12 digits budget amount is allowed.');
        return false;
    }
    let form = $(this);
    var loadingButton = jQuery(this).find('#btnEditSave');
    loadingButton.button('loading');
    if ($('#editDescription').summernote('isEmpty')) {
        $('#editDescription').summernote('code');
    }else if (empty){
        displayErrorMessage('Description field is not contain only white space');
        loadingButton.button('reset');
        return false;
    }
    let formdata = $(this).serializeArray();
    formdata[formdata.length] = {
        name: 'color',
        value: $('#edit_color').val(),
    };
    var id = $('#projectId').val();
    $.ajax({
        url: route('projects.update',id),
        type: 'put',
        data: formdata,
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#ProjectEditModal').modal('hide');
                $('#projects_table').DataTable().ajax.reload(null, false);
                revokerTracker();
                window.livewire.emit('refresh');
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message)
        },
        complete: function () {
            loadingButton.button('reset')
        },
    })
})

$(document).on('submit', '#addNewClientForm', function (event) {
    event.preventDefault();
    var loadingButton = jQuery(this).find('#btnClientSave');
    loadingButton.button('loading');
    $.ajax({
        url: '/client/store',
        type: 'POST',
        data:  $(this).serializeArray(),
        success: function (result) {
            $("#client_id").empty();
            if (result.success) {
                displaySuccessMessage(result.message);
                var option = "<option value=''>Select Client</option>";
                $.each(result.data.clients, function (key, value) {
                    option += "<option value='" + key + "'>" + value +
                        "</option>";
                    $("#client_id, #edit_client_id").html(option);
                });
                if ($('#AddModal').hasClass('show')) {
                    $('#client_id').val(result.data.client.id).trigger('change.select2');
                } else if ($('#ProjectEditModal').hasClass('show')) {
                    $('#edit_client_id').val(result.data.client.id).trigger('change.select2');
                }
                $('#addClientModal').modal('hide');
            }
        },
        error: function (result) {
            printErrorMessage('#clientValidationErrorsBox', result);
        },
        complete: function () {
            loadingButton.button('reset');
        },
    });
});

$('#addClientModal').on('hidden.bs.modal', function () {
    resetModalForm('#addNewClientForm', '#clientValidationErrorsBox');
    $('#department_id').val('').trigger('change.select2');
});

$('#AddModal').on('hidden.bs.modal', function () {
    $('#client_id').val(null).trigger('change');
    $('#user_ids').val(null).trigger('change');
    $('#currency').val(0).trigger('change');
    $('#budget_type').val(null).trigger('change');
    pickr.setColor('#42445A');
    pickr.hide();
    $('#description').summernote('code', '');
    resetModalForm('#addNewForm', '#validationErrorsBox');
});
$('#ProjectEditModal').on('show.bs.modal', function () {
    $('.pcr-button').css('border', '1px solid grey');
    $('.project_remaining_user').popover('hide');
});

$('#ProjectEditModal').on('hidden.bs.modal', function () {
    $('#editDescription').summernote('code', '');
    editPickr.hide();
    resetModalForm('#editFormProject', '#editValidationErrorsBox');
});

window.renderData = function (id) {
    $.ajax({
        url: route('projects.edit',id),
        type: 'GET',
        success: function (result) {
            if (result.success) {
                let project = result.data.project;
                let element = document.createElement('textarea');
                element.innerHTML = project.name;
                $('#projectId').val(project.id);
                $('#edit_name').val(element.value);
                $('#edit_prefix').val(project.prefix);
                editPickr.setColor(project.color);
                $('#edit_client_id').val(project.client_id).trigger('change');
                $('#editStatusProject').val(project.status).trigger('change');
                $('#editDescription').summernote('code', project.description);
                if (!isEmpty(project.price)) {
                    $('#editPrice').val(getFormattedPrice(project.price));
                }
                $('#editCurrency').val(project.currency).trigger('change');
                $('#edit_budget_type').
                    val(project.budget_type).
                    trigger('change');
                var valArr = result.data.users;
                $('#edit_user_ids').val(valArr);
                $('#edit_user_ids').trigger('change');
                $('#ProjectEditModal').modal('show');
            }
        },
        error: function (result) {
            manageAjaxErrors(result)
        },
    })
}

$(document).on('click', '.edit-btn', function (event) {
    let projectId = $(event.currentTarget).attr('data-id');
    renderData(projectId)

})

$(document).on('click', '.delete-btn', function (event) {
    let projectId = $(event.currentTarget).attr('data-id');
    let alertMessage = '<div class="alert alert-warning swal__alert">\n' +
        '<strong class="swal__text-warning">' + byDeleteThisProject +
        '</strong><div class="swal__text-message">' + deleteProjectConfirm +
        '</div></div>';
    let stopwatchProjectId = getItemFromLocalStorage('project_id')
    let isClockRunning = getItemFromLocalStorage('clockRunning')
    if (projectId === stopwatchProjectId && isClockRunning === 'true') {
        tbl.ajax.reload();
        swal({
            'title': 'Warning',
            'text': 'Please stop timer before delete project.',
            'type': 'warning',
            confirmButtonColor: '#6777ef',
        });
        return false;
    }

    deleteItemInputConfirmation(route('projects.destroy', projectId), '#projects_table',
        'Project', alertMessage)
    setTimeout(function () {
        revokerTracker()
    }, 1000)
});

$('.modal').on('show.bs.modal', function () {
    $(this).appendTo('body');
});

$(document).on('change', '#filterClient', function () {
    window.livewire.emit('filterProjects', $(this).val());
});

// $('#price,#editPrice').on('keyup', function () {
//     let regex = /^\d{0,6}?$/;
//     if (!regex.test($(this).val())) {
//         $(this).val('');
//     }
// });

$(document).on('click', '#all-projects', function () {
    projectStatusLivewire(null);
});

$('document').ready(function () {
    screenUnLock();
    $('#statusOngoing').trigger('click');
});

$(document).on('change', '#projectStatus', function () {
    let projectStatus = $(this).val();
    projectStatusLivewire(projectStatus);
});

window.projectStatusLivewire = function ($projectStatus) {
    window.livewire.emit('projectsStatus', $projectStatus);
};

$(document).on('change', '#myProjects', function () {
    let userId = $(this).data('id');

    if ($('#myProjects').prop('checked') == true) {
        window.livewire.emit('usersProject', userId);
    } else {
        window.livewire.emit('usersProject', null);
    }
});
document.addEventListener('livewire:load', function () {
    window.livewire.hook('message.processed', () => {
        $('#editProjectUser').select2({
            width: '100%',
        });
        $('#projectStatus').select2({
            width: '100%',
        });
    });
});

$(document).on('focusout', '#name', function (e) {
    let name = $(this).val();
    name = name.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '');
    $('#prefix').val(name.toUpperCase().slice(0, 8));
});

$(document).on('focusout', '#prefix', function (e) {
    let prefix = $(this).val();
    prefix = prefix.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '');
    $(this).val(prefix.toUpperCase().slice(0, 8));
});

$(document).on('focusout', '#edit_name', function (e) {
    let name = $(this).val();
    name = name.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '');
    $('#edit_prefix').val(name.toUpperCase().slice(0, 8));
});

$(document).on('focusout', '#edit_prefix', function (e) {
    let prefix = $(this).val();
    prefix = prefix.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '');
    $(this).val(prefix.toUpperCase().slice(0, 8));
});
