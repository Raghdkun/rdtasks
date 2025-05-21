'use strict';

$(function () {
    $('#projectId,#editProjectId').select2({
        width: '100%',
        placeholder: 'Select Projects',
    })
    $('#roleId,#editRoleId').select2({
        width: '100%',
        placeholder: 'Select Role',
        // minimumResultsForSearch: -1,
    })
    $('#filterStatus').select2({
       width:'100%',
    });
});

$(document).ready(function () {
    $('input').attr('autocomplete', 'false');
});

var tbl = $('#users_table').DataTable({
    processing: true,
    serverSide: true,
    'order': [[0, 'asc']],
    ajax: {
        url: route('users.index'),
    },
    columnDefs: [
        {
            'targets': [6],
            'orderable': false,
            'className': 'text-center',
            'width': '5%',
        },
        {
            'targets': [5],
            'orderable': false,
            'className': 'text-center',
            'width': '5%',
        },
        {
            'targets': [4],
            'className': 'text-center',
            'width': '4%',
        },
        {
            'targets': [3,7],
            'orderable': false,
            'className': 'text-center',
            'width': '6%',
        },
    ],
    columns: [
        {
            data: 'name',
            name: 'name',
        },
        {
            data: 'email',
            name: 'email',
        },
        {
            data: 'phone',
            name: 'phone',
        },
        {
            data: 'role_name',
            name: 'role_name',
            'searchable': false,
        },
        {
            data: 'salary',
            name: 'salary',
        },
        {
            data: function (row) {
                let checked = row.is_active === 0 ? '' : 'checked'
                if (loggedInUserId === row.id) {
                    return ''
                }
                return ' <label class="switch switch-label switch-outline-primary-alt">' +
                    '<input name="is_active" data-id="' + row.id +
                    '" class="switch-input is-active" type="checkbox" value="1" ' +
                    checked + '>' +
                    '<span class="switch-slider" data-checked="&#x2713;" data-unchecked="&#x2715;"></span>' +
                    '</label>'
            }, name: 'id',
        },
        {
            data: function (row) {
                var email_verification = '<button type="button" title="Send Verification Mail" id="email-btn" class="btn action-btn btn-primary btn-sm email-btn" ' +
                    'data-loading-text="<span class=\'spinner-border spinner-border-sm\'></span>" data-id="' +
                    row.id + '">' +
                    '<i class="icon-envelope icons action-icon"></i></button>'
                if (row.is_email_verified) {
                    email_verification = '<a title="Email Verified" data-id="' +
                        row.id + '">' +
                        '<i class="cui-circle-check check-icon"></i></a>'
                }
                return email_verification
            }, name: 'id',
        },
        {
            data: function (row) {
                return '<a title="Edit" class="btn action-btn btn-primary btn-sm edit-btn mr-1" data-id="' +
                    row.id + '">'
                    +
                    '<i class="cui-pencil action-icon user-js-action-color"></i>' +
                    '</a>' +
                    '<a title="Delete" class="btn action-btn btn-danger btn-sm delete-btn" data-id="' +
                    row.id + '">' +
                    '<i class="cui-trash action-icon text-danger"></i></a>'
            }, name: 'id',
        },
    ],
});

$('#users_table').on('draw.dt', function () {
    $('[data-toggle="tooltip"]').tooltip()
});

window.renderData = function (userId) {
    $.ajax({
        url: route('users.edit',userId),
        type: 'GET',
        success: function (result) {
            if (result.success) {
                let user = result.data;
                let element = document.createElement('textarea');
                element.innerHTML = user.name;
                $('#userId').val(user.id);
                $('#edit_name').val(element.value);
                $('#edit_email').val(user.email);
                $('#edit_phone').val(user.phone);
                $('#edit_salary').val(user.salary);
                $('.price-input').trigger('input');
                $('#editProjectId').val(user.project_ids).trigger('change');
                if (user.is_active) {
                    $('#edit_is_active').val(1).prop('checked', true);
                }
                if(user.email_verified_at) {
                    $('#edit_email_verified_at').val(1).prop('checked',true).attr('disabled', true);
                }else{
                    $('#edit_email_verified_at').attr('disabled', false);
                }
                $('#edit_email_verified_at').trigger('change');
                $('#editRoleId').val(user.role_id).trigger('change');

                if (!isEmpty(user.img_avatar)) {
                    $('#editPreviewImage').attr('src', user.img_avatar);
                } else {
                    $('#editPreviewImage').attr('src', defaultImageUrl);
                }
                $('#EditModal').modal('show');
            }
        },
        error: function (error) {
            manageAjaxErrors(error)
        },
    })
};

window.sendVerificationEmail = function (userId) {

    $.ajax({
        url: route('send-email',userId),
        type: 'GET',
        beforeSend: function beforeSend() {
            startLoader();
        },
        success: function (result) {
            if (result.success) {
                swal('Success!', result.message, 'success')
            }
        },
        error: function (error) {
            manageAjaxErrors(error)
        },
        complete: function () {
            stopLoader();
            $('.email-btn').html('<i class="fas fa-sync font-size-12px"></i>');
        },
    })
};

$('#new_password, #new_confirm_password').on('keypress', function (e) {
    if (e.which == 32){
        return false;
    }
});

$(function () {
    // create new user
    $(document).on('submit', '#addNewForm', function (event) {
        event.preventDefault();
        var loadingButton = jQuery(this).find('#btnSave');
        loadingButton.button('loading');
        $.ajax({
            url: route('users.store'),
            type: 'POST',
            data: new FormData($(this)[0]),
            processData: false,
            contentType: false,
            success: function (result) {
                if (result.success) {
                    displaySuccessMessage(result.message);
                    $('#AddModal').modal('hide');
                    $('#users_table').DataTable().ajax.reload(null, false);
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
    });

    // update user
    $(document).on('submit', '#editForm', function (event) {
        event.preventDefault();
        $('#edit_email_verified_at').attr('disabled', false);
        var loadingButton = jQuery(this).find('#btnEditSave');
        loadingButton.button('loading');
        var id = $('#userId').val();
        $.ajax({
            url: route('users-update',id),
            type: 'POST',
            data: new FormData($(this)[0]),
            processData: false,
            contentType: false,
            success: function (result) {
                if (result.success) {
                    displaySuccessMessage(result.message);
                    $('#EditModal').modal('hide');
                    $('#users_table').DataTable().ajax.reload(null, false);
                    if(window.location.pathname == '/users'){
                        window.livewire.emit('refresh');
                    }
                }
            },
            error: function (error) {
                manageAjaxErrors(error);
                $('#edit_email_verified_at').attr('disabled', true);
            },
            complete: function () {
                loadingButton.button('reset')
            },
        })
    });

    $('#AddModal').on('hidden.bs.modal', function () {
        $('#projectId').val(null).trigger('change')
        $('#roleId').val(null).trigger('change');
        $('#previewImage').attr('src', defaultImageUrl);
        resetModalForm('#addNewForm', '#validationErrorsBox');
    });

    $('#EditModal').on('hidden.bs.modal', function () {
        resetModalForm('#editForm', '#editValidationErrorsBox')
    });

    // open edit user model
    $(document).on('click', '.edit-btn', function (event) {
        let userId = $(event.currentTarget).attr('data-id');
        renderData(userId)
    })

    // open delete confirmation model
    $(document).on('click', '.delete-btn', function (event) {
        let userId = $(event.currentTarget).attr('data-id');
        let alertMessage = '<div class="alert alert-warning swal__alert">\n' +
            '<strong class="swal__text-warning">' + deleteMessage + ' ' + user + '?' +
            '</strong><div class="swal__text-message">' + deleteUserConfirm +
            '</div></div>';
        setTimeout(function () {
            revokerTracker()
        }, 1000)

            swal({
                    type: 'input',
                    inputPlaceholder: deleteConfirm + ' "' + deleteWord + '" ' +
                        toTypeDelete + ' ' + 'User' + '.',
                    title: deleteHeading + ' !',
                    text: alertMessage,
                    html: true,
                    showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                    confirmButtonColor: '#6777ef',
                    cancelButtonColor: '#d33',
                    cancelButtonText: noMessages,
                    confirmButtonText: yesMessages,
                    imageUrl: baseUrl + 'images/warning.png',
                },
                function (inputVal) {
                    if (inputVal === false) {
                        return false
                    }
                    if (inputVal == '' || inputVal.toLowerCase() != 'delete') {
                        swal.showInputError(
                            'Please type "delete" to delete this client.')
                        $('.sa-input-error').css('top', '23px!important');
                        $(document).find('.sweet-alert.show-input :input').val('');
                        return false
                    }
                    if (inputVal.toLowerCase() === 'delete') {
                        $.ajax({
                            url: route('users.destroy',userId),
                            type: 'DELETE',
                            dataType: 'json',
                            success: function (obj) {
                                if (obj.success) {
                                    window.livewire.emit('refresh');
                                }
                                swal({
                                    title: 'Deleted!',
                                    text: 'User has been deleted.',
                                    confirmButtonColor: '#6777ef',
                                    type: 'success',
                                    timer: 2000,
                                })
                            },
                            error: function (data) {
                                swal({
                                    title: '',
                                    text: data.responseJSON.message,
                                    confirmButtonColor: '#6777ef',
                                    type: 'error',
                                    timer: 5000,
                                })
                            },
                        })
                    }
                })
    });

    $(document).on('click', '.email-btn', function (event) {
        $(this).html('<i class="fas fa-sync font-size-12px fa-spin"></i>');
        let userId = $(event.currentTarget).attr('data-id');
        sendVerificationEmail(userId)
    })
});

// listen user activation deactivation change event
$(document).on('change', '.is-active', function (event) {
    const userId = $(event.currentTarget).attr('data-id');
    activeDeActiveUser(userId)
});

// activate de-activate user
window.activeDeActiveUser = function (id) {
    $.ajax({
        url: route('active-de-active-user',id),
        method: 'post',
        cache: false,
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                tbl.ajax.reload();
                window.livewire.emit('refresh');
            }
        },
        error: function (result) {
            manageAjaxErrors(result);
            setTimeout(location.reload(true), 700);
        },
    })
};

$('.modal').on('show.bs.modal', function () {
    $(this).appendTo('body');
});

$(document).on('change', '#userProfile', function () {
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

$(document).on('change', '#userEditProfile', function () {
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

$(document).on('change', '#filterStatus', function () {
    window.livewire.emit('filterUsers', $(this).val());
});

$(document).on('click', '.permanent-delete', function (event) {
    let id = $(event.currentTarget).attr('data-id');
        swal({
            title: deleteHeading + ' !',
            text: 'Are you sure  want to delete this "User" ?',
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#d33',
            cancelButtonText: noMessages,
            confirmButtonText: yesMessages
        }, function () {
            $.ajax({
                url: 'users/'+ id +'/delete',
                type: 'DELETE',
                dataType: 'json',
                success: function (obj) {
                    if (obj.success) {
                        window.livewire.emit('refresh');
                    }
                    swal({
                        title: 'Deleted!',
                        text: 'User has been deleted.',
                        confirmButtonColor: '#6777ef',
                        type: 'success',
                        timer: 2000,
                    })
                },
                error: function (data) {
                    swal({
                        title: '',
                        text: data.responseJSON.message,
                        confirmButtonColor: '#6777ef',
                        type: 'error',
                        timer: 5000,
                    })
                },
            })
        });
    });

$(document).on('click', '.restore-btn', function (event) {
    let id = $(event.currentTarget).attr('data-id');
    swal({
        title: 'Restore' + ' !',
        text: 'Are you sure  want to restore this User ?',
        type: 'info',
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
        confirmButtonColor: '#5cb85c',
        cancelButtonColor: '#d33',
        cancelButtonText: noMessages,
        confirmButtonText: yesMessages,
    }, function () {
        $.ajax({
            url: 'users/'+id +'/restore',
            type: 'POST',
            dataType: 'json',
            success: function (obj) {
                if (obj.success) {
                    window.livewire.emit('refresh');
                }
                swal({
                    title: 'Restored!',
                    text: 'User has been restored.',
                    type: 'success',
                    timer: 2000,
                })
            },
            error: function (data) {
                swal({
                    title: '',
                    text: data.responseJSON.message,
                    type: 'error',
                    timer: 5000,
                })
            },
        })
    });
});
