'use strict';

$(document).ready(function () {
    $('#currencyType').select2({
        width: '100%',
    });
});

$(document).on('submit', '.settings', function () {
    let loadingButton = jQuery(this).find('.save-btn');
    loadingButton.button('loading');
    $('.settings').find('input').each(function () {
        if ($(this).prop('required') && $(this).val().length === 0) {
            loadingButton.button('reset');
            return false;
        }
    });
});

$(document).on('change', '#appLogo', function () {
    if (isValidFile($(this), '#validationErrorsBox')) {
        displayPhoto(this, '#previewImage');
    }
});

$(document).on('click', '#showRecaptcha', function () {
    if($(this).prop("checked") != true){
        $('.google-recaptcha-site-key').removeAttr('required')
        $('.google-recaptcha-secret-key').removeAttr('required')
    }else{
        $('.google-recaptcha-site-key').attr('required','required')
        $('.google-recaptcha-secret-key').attr('required','required')
    }
});

window.isValidFile = function (inputSelector, validationMessageSelector) {
    let ext = $(inputSelector).val().split('.').pop().toLowerCase();
    if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
        $(inputSelector).val('');
        $(validationMessageSelector).
            html('The image must be a file of type: jpeg, jpg, png.').
            show();
        setTimeout(function (){
            $('#validationErrorsBox').delay(5000).slideUp(300);
        });
        return false;
    }
    return true;
};

window.displayPhoto = function (input, selector) {
    let displayPreview = true;
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            let image = new Image();
            image.src = e.target.result;
            image.onload = function () {
                $(selector).attr('src', e.target.result);
                displayPreview = true;
            };
        };
        if (displayPreview) {
            reader.readAsDataURL(input.files[0]);
            $(selector).show();
        }
    }
};

$(document).on('change', '#favicon', function () {
    $('#validationErrorsBox').addClass('d-none');
    if (isValidFavicon($(this), '#validationErrorsBox')) {
        displayFavicon(this, '#faviconPreview');
    }
});

window.isValidFavicon = function (inputSelector, validationMessageSelector) {
    let ext = $(inputSelector).val().split('.').pop().toLowerCase();
    if ($.inArray(ext, ['gif', 'png', 'ico']) == -1) {
        $(inputSelector).val('');
        $(validationMessageSelector).removeClass('d-none');
        $(validationMessageSelector).
            html('The image must be a file of type: gif, ico, png.').
            show();
        setTimeout(function (){
            $('#validationErrorsBox').delay(5000).slideUp(300);
        });
        return false;
    }
    $(validationMessageSelector).hide();
    return true;
};

window.displayFavicon = function (input, selector) {
    let displayPreview = true;
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            let image = new Image();
            image.src = e.target.result;
            image.onload = function () {
                if ((image.height != 16 || image.width != 16) && (image.height != 32 || image.width != 32)) {
                    $('#favicon').val('');
                    $('#validationErrorsBox').removeClass('d-none');
                    $('#validationErrorsBox').html('The image must be of pixel 16 x 16 and 32 x 32.').show();
                    setTimeout(function (){
                        $('#validationErrorsBox').delay(5000).slideUp(300);
                    });
                    return false;
                }
                $(selector).attr('src', e.target.result);
                displayPreview = true;
            };
        };
        if (displayPreview) {
            reader.readAsDataURL(input.files[0]);
            $(selector).show();
        }
    }
};

$(document).on('keyup', '#workingDays', function () {
    let val = $(this).val();
    if (val > 31) {
        $(this).val(31);
    }
});

$(document).on('keyup', '#workingHours', function () {
    let val = $(this).val();
    if (val > 24) {
        $(this).val(24);
    }
});

$(document).on('click', '.save-btn-invoice', function () {
    const loadingButton = jQuery(this);
    loadingButton.button('loading');
    $('.invoice-settings').submit();
});

$(document).ready(function () {
    $('.pcr-button').css({ 'border': '1px solid grey' });
});
