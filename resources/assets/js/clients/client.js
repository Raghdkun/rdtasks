$(document).ready(function () {
    'use strict';

    $('#department_id,#edit_department_id').select2({
        width: canManageDepartment?'calc(100% - 44px)':'100%',
        placeholder: 'Select Department',
    });

    $('#filter_department').select2();

    $('#AddModal').on('show.bs.modal', function (){
        $(this).find('#password').slideUp();
    });

    $('#EditModal').on('shown.bs.modal', function () {
        $(this).find('#new_edit_password').val('');
    });

    $(document).on('click','#addClient', function (){
        let model = $('#AddModal');
        if($(this).prop('checked')){
           model.find('#password').slideDown();
           model.find('#new_password').attr('required',true);
           model.find('#new_confirm_password').attr('required',true);
       } else{
           model.find('#password').slideUp();
           model.find('#new_password').removeAttr('required');
           model.find('#new_password').val('');
           model.find('#new_confirm_password').removeAttr('required');
           model.find('#new_confirm_password').val('');
       }
    });

    $('#new_password, #new_confirm_password, #new_edit_password, #new_confirm_edit_password').on('keypress', function(e) {
        if (e.which == 32){
            return false;
        }
    });

    $(document).on('submit', '#addNewForm', function (event) {
        event.preventDefault();
        var loadingButton = jQuery(this).find('#btnSave');
        loadingButton.button('loading');
        if ($('#addClient').prop('checked') == false && $('#new_password').val() != '') {
            $('#new_password').val('');
        }
        $.ajax({
            url: route('clients.store'),
            type: 'POST',
            data: new FormData($(this)[0]),
            processData: false,
            contentType: false,
            success: function (result) {
                if (result.success) {
                    displaySuccessMessage(result.message);
                    $('#AddModal').modal('hide');
                    $('#clients_table').DataTable().ajax.reload(null, false);
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

    $(document).on('submit', '#addNewDepartment', function (event) {
        event.preventDefault();
        let loadingButton = jQuery(this).find('#btnDepartmentSave');
        loadingButton.button('loading');
        let formdata = $(this).serializeArray();

        $.ajax({
            url: 'departments',
            type: 'POST',
            data: formdata,
            success: function (result) {
                $("#department_id, #edit_department_id").empty();
                if (result.success) {
                    displaySuccessMessage(result.message);
                    var option = "<option value=''>Select Department</option>";
                    $.each(result.data.departments, function(key, value) {
                        option += "<option value='"+value+"'>"+key+"</option>";
                        $("#department_id, #edit_department_id").html(option);
                    });
                    if ($('#AddModal').hasClass('show')) {
                        $('#department_id').
                            val(result.data.department.id).
                            trigger('change.select2');
                    } else if ($('#EditModal').hasClass('show')) {
                        $('#edit_department_id').
                            val(result.data.department.id).
                            trigger('change.select2');
                    }
                    $('#addDepartmentModal').modal('hide');
                }
            },
            error: function (result) {
                printErrorMessage('#departmentValidationErrorsBox', result);
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
        var id = $('#clientId').val();
        $.ajax({
            url: route('clients-update',id),
            type: 'POST',
            data: new FormData($(this)[0]),
            processData: false,
            contentType: false,
            success: function (result) {
                if (result.success) {
                    displaySuccessMessage(result.message);
                    $('#EditModal').modal('hide');
                    $('#clients_table').DataTable().ajax.reload(null, false);
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

    $('#addDepartmentModal').on('hidden.bs.modal', function () {
        resetModalForm('#addNewDepartment', '#validationErrorsBox');
    });

    $('#AddModal').on('hidden.bs.modal', function () {
        resetModalForm('#addNewForm', '#validationErrorsBox');
        $('#previewImage').attr('src', defaultImageUrl);
        $('#department_id').val('').trigger('change.select2');
    });

    $('#EditModal').on('hidden.bs.modal', function () {
        $('#delete-warning').empty();
        resetModalForm('#editForm', '#editValidationErrorsBox');
    });

    window.renderData = function (id) {
        $.ajax({
            url: route('clients.edit',id),
            type: 'GET',
            success: function (result) {
                if (result.success) {
                    let client = result.data;
                    let element = document.createElement('textarea');
                    element.innerHTML = client.name;
                    $('#clientId').val(client.id);
                    $('#edit_name').val(element.value);
                    $('#edit_email').val(client.email);
                    $('#edit_department_id').
                        val(client.department_id).
                        trigger('change.select2');
                    $('#edit_website').val(client.website);
                    if (!isEmpty(client.avatar)) {
                        $('#editPreviewImage').attr('src', client.avatar);
                    } else {
                        $('#editPreviewImage').attr('src', defaultImageUrl);
                    }
                    if(client.email && client.user_id != null){
                        $('#delete-warning').append('(If you save this record without email all records of this client will be deleted)');
                    }
                    $('#EditModal').modal('show');
                }
            },
            error: function (result) {
                manageAjaxErrors(result);
            },
        });
    };
    $(document).on('click', '.edit-btn', function (event) {
        let clientId = $(event.currentTarget).attr('data-id');
        renderData(clientId);
    });

    $(document).on('click', '.delete-btn', function (event) {
        let clientId = $(event.currentTarget).attr('data-id');
        let alertMessage = '<div class="alert alert-warning swal__alert">\n' +
            '<strong class="swal__text-warning">' + deleteClientConfirm +
            '</strong><div class="swal__text-message">' + byDeleteThisClient +
            '</div></div>';

        deleteItemInputConfirmation(route('clients.destroy',clientId), '#clients_table',
            'Client', alertMessage);
    });

    $('.modal').on('show.bs.modal', function () {
        $(this).appendTo('body');
    });

    $(document).on('change', '#filter_department', function () {
        window.livewire.emit('filterDepartment', $(this).val());
    });

    $(document).on('change', '#clientProfile', function () {
        let ext = $(this).val().split('.').pop().toLowerCase();
        if ($.inArray(ext, ['png', 'jpg', 'jpeg']) == -1) {
            $(this).val('');
            $('#validationErrorsBox').html('The profile image must be a file of type: jpeg, jpg, png.').show();
            setTimeout(function () {
                $('#validationErrorsBox').slideUp();
            }, 5000);
        } else {
            displayPhoto(this, '#previewImage');
        }
    });

    $(document).on('change', '#clientEditProfile', function () {
        let ext = $(this).val().split('.').pop().toLowerCase();
        if ($.inArray(ext, ['png', 'jpg', 'jpeg']) == -1) {
            $(this).val('');
            $('#editValidationErrorsBox').html('The profile image must be a file of type: jpeg, jpg, png.').show();
            setTimeout(function () {
                $('#editValidationErrorsBox').slideUp();
            }, 5000);
        } else {
            displayPhoto(this, '#editPreviewImage');
        }
    });

    $(document).on('click', '.show-btn', function (event) {
        let clientId = $(event.currentTarget).attr('data-id');
        $.ajax({
            url: route('clients.show',clientId),
            type: 'GET',
            success: function (result) {
                if (result.success) {
                    $('#showImage').show();
                    $('#showName').html('');
                    $('#showDepartment').html('');
                    $('#showEmail').html('');
                    $('#showWebsite').html('');
                    $('.progressProjectFinished').html('');
                    $('.progressProjectInProgress').html('');
                    $('.progressProjectHold').html('');
                    $('.progressProjectArchived').html('');
                    $('#showName').append(result.data.name);
                    $('#showDepartment').append(result.data.department.name);
                    if (isEmpty(result.data.email)) {
                        $('#showEmail').append('N/A');
                    } else {
                        $('#showEmail').append(result.data.email);
                    }
                    if (isEmpty(result.data.website)) {
                        $('#showWebsite').append('N/A');
                    } else {
                        $('#showWebsite').append(result.data.website);
                    }
                    if (!isEmpty(result.data.media)) {
                        $('#showImage').attr('src', result.data.avatar);
                        $('#noImage').hide();
                    } else {
                        $('#noImage').hide();
                        $('#showImage').attr('src', defaultImageUrl);
                    }

                    $('.progressProjectFinished').append(result.data.project_progress.completedProjects);
                    $('.progressProjectInProgress').append(result.data.project_progress.openProjects);
                    $('.progressProjectHold').append(result.data.project_progress.holdProjects);
                    $('.progressProjectArchived').append(result.data.project_progress.archivedProjects);

                    $('#progressProjectFinished').css('width','100%');
                    $('#progressProjectInProgress').css('width','100%');
                    $('#progressProjectHold').css('width','100%');
                    $('#progressProjectArchived').css('width','100%');

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
        });
    });

    $(document).on('blur', '#website', function () {
        var website = $(this).val();
        if (!isEmpty(website)) {
            website = websiteURLConvert(website);
            $('#website').val(website);
        }
    });

    $(document).on('blur', '#edit_website', function () {
        var edit_website = $(this).val();
        if (isEmpty(edit_website)) {
            $('#edit_website').val('');
        } else {
            edit_website = websiteURLConvert(edit_website);
            $('#edit_website').val(edit_website);
        }
    });

    window.websiteURLConvert = function (website) {
        if (!~website.indexOf('http')) {
            website = 'http://' + website;
        }

        return website;
    };
});
