'use strict';

$(document).ready(function () {

    $('#description, #editDescription').summernote({
        placeholder: 'Add Role description...',
        minHeight: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['para', ['paragraph']]],
    });

    $(document).on('submit', '.role-form', function () {
        let loadingButton = jQuery(this).find('.save-btn');
        loadingButton.button('loading');
        $('.role-form').find('input').each(function () {
            if ($(this).prop('required') && $(this).val().length === 0) {
                loadingButton.button('reset');
                return false;
            }
        });
        let $description = $('<div />').html($('#description').summernote('code'));
        let empty = $description.text().trim().replace(/ \r\n\t/g, '') === '';
        if($('#description').summernote('isEmpty')){
            $('#description').val('');
        } else if (empty){
            displayErrorMessage('Description field is not contain only white space');
            loadingButton.button('reset');
            return false;
        }
    });

    $(document).on('click', '.edit-role', function () {
        const loadingButton = jQuery(this);
        loadingButton.button('loading');
        $('.role-form').submit();
    });

    $(document).on('submit', '#editRoleForm', function () {
        let loadingButton = jQuery(this).find('.save-btn');
        loadingButton.button('loading');
        let $description = $('<div />').html($('#editDescription').summernote('code'));
        let empty = $description.text().trim().replace(/ \r\n\t/g, '') === '';
        if($('#editDescription').summernote('isEmpty')){
            $('#editDescription').val('');
        } else if (empty){
            displayErrorMessage('Description field is not contain only white space');
            loadingButton.button('reset');
            return false;
        }
    });
});
