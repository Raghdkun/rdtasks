$(document).ready(function () {
    'use strict';

    $('#language').select2({
        width: '100%',
    });

    $(document).on('click', '.edit-profile', function (event) {
        let userId = $(event.currentTarget).attr('data-id');
        renderProfileData(userId)
    });
    
    $(document).on('submit', '#editProfileForm', function (event) {
        event.preventDefault();
        let loadingButton = jQuery(this).find('#btnPrEditSave');
        loadingButton.button('loading');
        $.ajax({
            url: route('update-profile'),
            type: 'post',
            data: new FormData($(this)[0]),
            processData: false,
            contentType: false,
            success: function (result) {
                if (result.success) {
                    $('#EditProfileModal').modal('hide');
                    displaySuccessMessage(result.message);
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                }
            },
            error: function (result) {
                UnprocessableInputError(result);
            },
            complete: function () {
                loadingButton.button('reset');
            },
        });
    });
    
    
    $(document).on('click', '.changePasswordModal', function () {
        $('#changePasswordModal').appendTo('body').modal('show');
    });

    $(document).on('submit', '#changePasswordForm', function (event) {
        event.preventDefault();
        let isValidate = validatePassword();
        if (!isValidate) {
            return false;
        }
        let loadingButton = jQuery(this).find('#btnPrPasswordEditSave');
        loadingButton.button('loading');
        $.ajax({
            url: route('change-password'),
            type: 'post',
            data: new FormData($(this)[0]),
            processData: false,
            contentType: false,
            success: function (result) {
                if (result.success) {
                    $('#ChangePasswordModal').modal('hide');
                    displaySuccessMessage(result.message);
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                }
            },
            error: function (result) {
                manageAjaxErrors(result, 'editPasswordValidationErrorsBox');
            },
            complete: function () {
                loadingButton.button('reset');
            },
        });
    });
    
    $('#EditProfileModal').on('hidden.bs.modal', function () {
        resetModalForm('#editProfileForm', '#editProfileValidationErrorsBox');
    });

    $('#changePasswordModal').on('hidden.bs.modal', function () {
        resetModalForm('#changePasswordForm', '#editPasswordValidationErrorsBox');
    });

    // open edit user profile model
    
    $(document).on('change', '#pfImage', function () {
        let ext = $(this).val().split('.').pop().toLowerCase();
        if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
            $(this).val('');
            $('#editProfileValidationErrorsBox').
                html(
                    'The profile image must be a file of type: jpeg, jpg, png.').
                show();
        } else {
            displayPhoto(this, '#edit_preview_photo');
        }
    });

    window.renderProfileData = function (userId) {
        $.ajax({
            url: route('client-edit-profile',userId),
            type: 'GET',
            success: function (result) {
                if (result.success) {
                    let client = result.data;
                    let element = document.createElement('textarea');
                    element.innerHTML = client.name;
                    $('#pfClientId').val(client.id)
                    $('#pfUserId').val(client.user_id)
                    $('#pfName').val(element.value);
                    $('#pfEmail').val(client.email)
                    $('#edit_preview_photo').attr('src', client.avatar);
                    $('#EditProfileModal').appendTo('body').modal('show');
                }
            },
        })
    }
    window.displayPhoto = function (input, selector) {
        let displayPreview = true
        if (input.files && input.files[0]) {
            let reader = new FileReader()
            reader.onload = function (e) {
                let image = new Image()
                image.src = e.target.result
                image.onload = function () {
                    $(selector).attr('src', e.target.result)
                    displayPreview = true
                }
            }
            if (displayPreview) {
                reader.readAsDataURL(input.files[0])
                $(selector).show()
            }
        }
    }

    $(document).on('keyup', '#name', function (e) {
        let txtVal = $(this).val().trim()
        txtVal = txtVal.replace(/[^a-z0-9]+|\s+/gmi, '');
        if ((e.charCode === 8 || (e.charCode >= 65 && e.charCode <= 90) ||
            (e.charCode >= 95 && e.charCode <= 122)) ||
            (e.charCode === 0 || (e.charCode >= 48 && e.charCode <= 57))) {
            if (txtVal.length <= 8) {
                $('#prefix').val(txtVal.toLocaleUpperCase())
            }
        }
    })

    $(document).on('keyup', '#edit_name', function (e) {
        let txtVal = $(this).val().trim()
        txtVal = txtVal.replace(/[^a-z0-9]+|\s+/gmi, '');
        if ((e.charCode === 8 || (e.charCode >= 65 && e.charCode <= 90) ||
            (e.charCode >= 95 && e.charCode <= 122)) ||
            (e.charCode === 0 || (e.charCode >= 48 && e.charCode <= 57))) {
            if (txtVal.length <= 8) {
                $('#edit_prefix').val(txtVal.toLocaleUpperCase())
            }
        }
    })

    $('.confirm-pwd').hide()
    $(document).on('blur', '#pfCurrentPassword', function () {
        let currentPassword = $('#pfCurrentPassword').val()
        if (currentPassword == '' || currentPassword.trim() == '') {
            $('.confirm-pwd').hide()
            return false
        }

        $('.confirm-pwd').show()
    })
    $('.confirm-pwd').hide()
    $(document).on('blur', '#pfNewPassword', function () {
        let password = $('#pfNewPassword').val()
        if (password == '' || password.trim() == '') {
            $('.confirm-pwd').hide()
            return false
        }

        $('.confirm-pwd').show()
    })
    $(document).on('blur', '#pfNewConfirmPassword', function () {
        let confirmPassword = $('#pfNewConfirmPassword').val()
        if (confirmPassword == '' || confirmPassword.trim() == '') {
            $('.confirm-pwd').hide()
            return false
        }

        $('.confirm-pwd').show()
    })

    function validatePassword () {
        let currentPassword = $('#pfCurrentPassword').val().trim()
        let password = $('#pfNewPassword').val().trim()
        let confirmPassword = $('#pfNewConfirmPassword').val().trim()

        if (currentPassword == '' || password == '' || confirmPassword == '') {
            $('#editPasswordValidationErrorsBox').
                show().
                html('Please fill all the required fields.')
            return false
        }
        return true
    }

    $(document).on('click', '.changeType', function () {
        let inputField = $(this).parent().siblings();
        let oldType = inputField.attr('type');
        if (oldType == 'password') {
            $(this).children().addClass('icon-eye');
            $(this).children().removeClass('icon-ban');
            inputField.attr('type', 'text');
        } else {
            $(this).children().removeClass('icon-eye');
            $(this).children().addClass('icon-ban');
            inputField.attr('type', 'password');
        }
    })
});

$(document).on('click','.changeLanguage', function () {
    let languageName = $(this).data('prefix-value');

    $.ajax({
        type: 'POST',
        url: route('update-language'),
        data: { languageName: languageName },
        success: function () {
            Lang.setLocale(languageName)
            location.reload();
        },
    });
});
