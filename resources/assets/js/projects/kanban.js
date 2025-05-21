"use strict";

let containers = [];
let boardCount = document.getElementsByClassName('board').length;

for (let i = 0; i<boardCount; i++) {
    containers.push(document.querySelector('.board-'+i));
}
let id;
let drake = dragula({
    containers: containers,
    revertOnSpill: true,
    direction: 'vertical'
}).on('drag', function (el) {
    el.className = el.className.replace('ex-moved', '');
}).on('drop', function (el, container) {
    let board = $(container);
    el.className += ' ex-moved';
    id = $('.ex-moved').data('id');
    let taskStatus = $('.ex-moved').data('task-status');
    let boardStatus = $(container).data('board-status');
    board.parent().find('.infy-loader').fadeIn();
    $.ajax({
        url: route('tasks.update-status',id),
        type: 'PUT',
        data: {
            task_status: taskStatus,
            status: boardStatus,
        },
        cache: false,
        complete: function () {
            board.parent().find('.infy-loader').fadeOut();
        }
    })
}).on('over', function (el, container) {
    container.className += ' ex-over';
}).on('out', function (el, container) {
    container.className = container.className.replace('ex-over', '');
});

$(document).ready(function() {
    let containers = [
        document.querySelector('.flex-nowrap')
    ];

    $('.board').each(function (index, ele) {
        containers.push(document.querySelector('.board-'+index));
    });

    var scroll = autoScroll(containers,{
        margin: 200,
        autoScroll: function(){
            return this.down && drake.dragging;
        }
    });
});

$('#dueDate').datetimepicker({
    format: 'YYYY-MM-DD',
    useCurrent: false,
    locale: languageName == 'ar' ? 'en' : languageName,
    icons: {
        previous: 'icon-arrow-left icons',
        next: 'icon-arrow-right icons',
    },
    sideBySide: true,
    minDate: moment().millisecond(0).second(0).minute(0).hour(0),
});

$(document).on('click', '.task-details', function (e) {
    e.preventDefault();
    let curEle = $(this);
    $(this).addClass('disabled');
    let taskId = $(this).attr('data-id');
    $.ajax({
        url: route('kanban-task-details',taskId),
        type: 'get',
        success: function (result) {
            if (result.success) {
                renderTaskDetailsKanban(result.data);
                $('#taskKanbanDetailsModel').modal('show');
                scrollToTheBottomComment();
            }
        },
        error: function (result) {
            manageAjaxErrors(result)
        },
        complete:function (){
            curEle.removeClass('disabled');
        }
    })
});

const renderTaskDetailsKanban = (data) => {
    let tasks_details = data.taskDetails;
    let status = data.taskStatus;
    let priority = data.priority;
    let users = data.users;
    let image = [] ,comments = [] ,attachments = [];
    image.push(tasks_details.task_assignee);
    comments.push(tasks_details.comments);
    attachments.push(data.attachments);
    let loginUser = $('#userId').val();

    $('#taskId').val(tasks_details.id);
    $('#task_title').empty();
    $('#task_title').append(tasks_details.title);
    $('#task_timer_card_16,#task_timer_all_card_16').
        append(roundToQuarterHourAll(tasks_details.task_total_minutes));
    (!isEmpty(tasks_details.description)) ? $('#task_description').
        append(tasks_details.description) : $('#task_no_description').
        append('No description added yet.')
    $('#start_date').
        append(moment(tasks_details.created_at).format('Do MMM, YYYY'))
    $('#status').
        append(prepareTemplateRender('#status-text',
            [{ 'status': tasks_details.status_text }]));
    if (!isEmpty(tasks_details.priority)) {
        $('#priority').
            append(prepareTemplateRender('#priority-text',
                [{ 'priority': tasks_details.priority }]));
    } else {
        $('#priority').
            append(prepareTemplateRender('#priority-text',
                [{ 'priority': 'N/A' }]));
    }
    $('#created_by').append(tasks_details.created_user.name);
    $('#created_date').
        append(moment(tasks_details.created_at).format('DD-MM-YYYY'));
    $('#project_id').
        append('#' + tasks_details.project.name).
        attr('href', 'user-assign-projects/' + tasks_details.project.id);
    $('#project_admin_id').
        append('#' + tasks_details.project.name).
        attr('href', 'projects/' + tasks_details.project.id);
    if (!isEmpty(tasks_details.due_date)) {
        $('#due_date').
            append(prepareTemplateRender('#edit-due-date', [
                {
                    'due_date': moment(tasks_details.due_date).
                        format('Do MMM, YYYY'),
                }]));
    } else {
        $('#due_date').
            append(prepareTemplateRender('#edit-due-date',
                [{ 'due_date': moment(moment()).format('Do MMM, YYYY') }]));
    }
    // $('#popover-content-status').
    //     append(prepareTemplateRender('#status-popover'));
    // $('#popover-content-priority').append(prepareTemplateRender('#priority-popover'));
    // $('#popover-content-edit-assignee').append(prepareTemplateRender('#assignee-popover'));

    $.each(status, function (i, val) {
        $('#popover-content-status').
            append(
                '<div class="status popover-div cursor-pointer pl-2" id="' + i +
                '"><span>' + val + '</span></div>');
    });
    $.each(priority, function (i, val) {
        $('#popover-content-priority').
            append(
                '<div class="priority popover-div cursor-pointer pl-2" id="' +
                i + '"><span>' + val + '</span></div>');
    });
    $.each(users, function (i, val) {
        $('#popover-content-edit-assignee').
            append(
                '<div class="edit-assignee popover-div cursor-pointer pl-1" id="' +
                i + '"><span>' + val + '</span></div>');
    });
    if(data.attachments.length !== 0 ) {
        for (let i = 0; attachments[0].length > i; i++) {
            let ext = data.attachments[i].name.split('.').pop();
            let image = [];
            let attachments_data = [];
            if (ext === 'png' || 'jpg' || 'jpeg' || 'PNG' || 'JPG'){
                image = data.attachments[i].url;
            }
            if (ext === 'xlsx' || ext === 'xls' || ext === 'csv' ){

                image = '/assets/img/xls_icon.png';
            }
            if(ext === 'pdf'){
                image = '/assets/img/pdf_icon.png';
            }
            if(ext === 'docx' || ext === 'doc' ){
                image ='/assets/img/doc_icon.png';
            }
            attachments_data = [{
                'id' : data.attachments[i].id,
                'url' : data.attachments[i].url,
                'image' : image,
                'username' : tasks_details.created_user.name,
                'downloadTask': route('tasks.index'),
                'updated_at'  :  moment(tasks_details.attachments[i].updated_at).fromNow(),
                'createdId' : tasks_details.created_user.id,
                'loginUserId' : loggedInUserId,
            }];
            $('#attachments').append(prepareTemplateRender('#attachment',attachments_data));
        }
    }else {
        $('#no_attachments').append('No attachments added yet').addClass('text-center');
    }
    if(tasks_details.comments.length !== 0 ){
        for (let i = 0; comments[0].length > i; i++) {
            let comment_data = [];
            comment_data = [{
                'id' :  tasks_details.comments[i].id,
                'userImage' : tasks_details.comments[i].created_user.img_avatar,
                'userName' : tasks_details.comments[i].created_user.name,
                'comment' : tasks_details.comments[i].comment,
                'updated_at' :  moment(tasks_details.comments[i].updated_at).fromNow(),
                'userId' : tasks_details.comments[i].created_user.id,
                'loginUserId' : loginUser,
            }];
            $('#card-comments-container').append(prepareTemplateRender('#comment',comment_data));
        }
    }else {
        $('#card-comments-container').append('<div class="text-center no-comment">No comments added yet</div>');
    }
    for (let i = 0; image[0].length > i; i++)
    {
        let assignee_data = [];
        assignee_data = [{
            'name' :  tasks_details.task_assignee[i].name,
            'avatar' : tasks_details.task_assignee[i].img_avatar,
            'moreAssignee' : image[0].length - 6,
        }];
        if(i < 6){
            $('#task_assignee').append(prepareTemplateRender('#task-assignee',assignee_data));
        }else if(i == (image[0].length - 1)){
            $('#task_assignee').append(prepareTemplateRender('#more-assignee',assignee_data));
        }
    }
    if(users != ''){

        $('#task_assignee').append(prepareTemplateRender('#add-assignee',[{'id' : tasks_details.id}]));
    }
}

$('#taskKanbanDetailsModel').on('hide.bs.modal', function () {
    if(TaskDescription.next().is(":visible")) {
        displayErrorMessage('Please Save Description');
        return false;
    }
    if ($('.task-title-input').hasClass('error')) {
        return false;
    }
});

$('#taskKanbanDetailsModel').on('hidden.bs.modal', function () {
    $(this).find('#task_title,#task_assignee,#task_description,#task_timer_card_16,#task_timer_all_card_16,#task_assignee,#start_date,#due_date,#status,#priority,#task_id,#created_by,#created_date,#project_id,#project_admin_id,#card-comments-container,#attachments,#task_no_description,#no_attachments,#popover-content-status,#popover-content-priority,#popover-content-edit-assignee').empty();
    $('.btn-upload').hide();
    $('.choose-button').show();
    commentsNote.next().hide();
    $('#btnCommentSave').hide();
    $('#btnCommentClose').hide();
    $('.comment-input').show();
    window.livewire.emit('refresh');
});

let currentInput;
let oldTaskName;
$(document).on('click', '#task_title,.edit-title', function () {
    oldTaskName = $('#task_title').text()
    $('#task_title').hide();
    $('.edit-title').hide();
    currentInput = $('#task_title').prev('input');
    currentInput.show().val(oldTaskName).focus();
});

$(document).on('blur', '.task-title-input', function (e) {
    e.preventDefault();
    let taskId = $('#taskId').val();
    let taskName = $(this).val();

    if (taskName.trim() == '') {
        $(this).addClass('error');
        displayErrorMessage('Enter task name');
        return false;
    }
    $(this).removeClass('error');
    $.ajax({
        url :  route('kanban-task-details-update',taskId),
        type : 'post',
        data : {title : taskName},
        success: function (result){
            $('.task-title-input').hide();
            $('.edit-title').show();
            $('#task_title').text('');
            if(result.success){
                $('#task_title').append(result.data.task.title).show();
            }
        },
        error: function (result) {
            manageAjaxErrors(result);
        },
    });
});

let TaskDescription = $('.description_input').summernote({
    placeholder:'Add Task Description...',
    minHeight: 200,
    toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough']],
        ['para', ['paragraph']]],
});
TaskDescription.next().hide();
$('.btnDescription').hide();

let oldTaskDescription;
$(document).on('click', '#task_description,.task_edit_description', function () {
    oldTaskDescription = $('#task_description').html();
    $('#task_description').hide();
    $('.task_edit_description').hide();
    $('#task_no_description').hide();
    $('.btnDescription').show();
    $('.description_input').summernote('code',oldTaskDescription);
    TaskDescription.next().addClass('border border-primary p-1 rounded').show();
    $('.description_input').summernote({focus:true});
});

$(document).on('click', '.btnDescription', function () {
    const loadingButton = jQuery(this);
    loadingButton.button('loading');
    let taskId = $('#taskId').val();
    let taskDescription = $('.description_input').summernote('code');
    let $description = $('<div />').html($('.description_input').summernote('code'));
    let empty = $description.text().trim().replace(/ \r\n\t/g, '') === '';
    if(TaskDescription.summernote('code') == ''){
        taskDescription = $('.description_input').summernote('code');
    } else if (empty){
        displayErrorMessage('Description field is not contain only white space');
        loadingButton.button('reset');
        return false;
    }
    $.ajax({
        url :  route('kanban-task-details-update',taskId),
        type : 'post',
        data : {description : taskDescription},
        success: function (result){
            TaskDescription.next().hide();
            $('.btnDescription').hide();
            $('.task_edit_description').show();
            $('#task_description').text('');
            if(result.success){
                $('#task_no_description').empty();
                $('#task_description').append(result.data.task.description).show();
                if(isEmpty(result.data.task.description)){
                    $('#task_no_description').append('No Description Added Yet').show();
                }
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

let commentsNote = $('#comments-input').summernote({
    placeholder: 'Add Comment...',
    minHeight: 200,
    toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough']],
        ['para', ['paragraph']]],
});
commentsNote.next().hide();
$('#btnCommentSave').hide();
$('#btnCommentClose').hide();

$(document).on('click', '.comment-input', function () {
    $(this).hide();
    commentsNote.next().addClass('border border-primary p-1 rounded').show();
    $('#comments-input').summernote({focus:true});
    $('#btnCommentSave').show();
    $('#btnCommentClose').show();
});

$(document).on('click', '#btnCommentClose', function () {
    $(this).hide();
    commentsNote.next().hide();
    $('#btnCommentSave').hide();
    $('.comment-input').show();
    commentsNote.summernote('code','');
    $('#btnCommentSave').data('edit-mode', 0);
});

window.scrollToTheBottomComment = function () {
    setTimeout(function () {
        let height = $('#card-comments-container').outerHeight();
        $('.comment-content').scrollTop(height * height);
    }, 200);
};

$(document).on('click', '#btnCommentSave', function () {
    const loadingButton = jQuery(this);
    loadingButton.button('loading');
    let curEle = $(this);
    $(this).addClass('disabled');
    let comment = commentsNote.summernote('code');
    let $description = $('<div />').html($('#comments-input').summernote('code'));
    let empty = $description.text().trim().replace(/ \r\n\t/g, '') === '';
    let taskId = $('#taskId').val();
    let commentId = $(this).data('comment-id');
    const editMode = $(this).data('edit-mode');
    let commentUrl = null;
    let loginUser = $('#userId').val();

    if (commentsNote.summernote('isEmpty')) {
        displayErrorMessage('Please Enter comment')
        curEle.removeClass('disabled')
        loadingButton.button('reset')
        return false
    } else if (empty) {
        displayErrorMessage('Comment is not contain only white space')
        curEle.removeClass('disabled')
        loadingButton.button('reset')
        return false
    }
    if (editMode === 0) {
        commentUrl = route('task.comments', taskId)
    } else {
        commentUrl = route('task.update-comment', [taskId, commentId])
    }
    $.ajax({
        url: commentUrl,
        type: 'post',
        data: { 'comment': comment },
        success: function (result) {
            if (result.success) {
                commentsNote.next().hide()
                $('#btnCommentSave').hide()
                $('#btnCommentClose').hide()
                $('.no-comment').remove()
                $('.comment-input').show();
                commentsNote.summernote('code','');
                if (editMode === 0){
                    let comments = result.data.comment;
                    let comment_data = [];
                    comment_data = [{
                        'id' :  comments.id,
                        'userImage' : comments.created_user.img_avatar,
                        'userName' : comments.created_user.name,
                        'comment' : comments.comment,
                        'updated_at' :  moment(comments.updated_at).fromNow(),
                        'userId' : comments.created_user.id,
                        'loginUserId' : loginUser,
                    }];
                    scrollToTheBottomComment();
                    $('#card-comments-container').append(prepareTemplateRender('#comment',comment_data));
                }
                else {
                    $('.comments-data' + commentId).html(comment);
                    $('#btnCommentSave').data('edit-mode', 0);
                    $('#btnCommentSave').data('comment-id', 0);
                }
            }
        },
        error: function (result) {
            printErrorMessage('#taskValidationErrorsBox', result);
        },
        complete:function (){
            curEle.removeClass('disabled');
            loadingButton.button('reset');
        }
    });
});

$(document).on('click', '.delete-comment', function () {
    let commentId = $(this).data('id');
    let taskId = $('#taskId').val();
    let divId = $(this).parent().parent().parent().parent().parent().parent().prop('id');
    swal({
            title: 'Delete !',
            text: 'Are you sure want to delete this "Comment" ?',
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#6777EF',
            cancelButtonColor: '#d33',
            cancelButtonText: 'No',
            confirmButtonText: 'Yes',
        },
        function () {
            $.ajax({
                url: route('task.delete-comment',[taskId,commentId]),
                type: 'DELETE',
                success: function (result) {
                    if (result.success) {
                        $('#'+divId+'').remove();
                        commentsNote.summernote('code', '');
                        $('#btnCommentSave').data('edit-mode', 0);
                        $('#btnCommentSave').data('comment-id', 0);
                        if(!$.trim($('#card-comments-container').html()))
                        {
                            $('#card-comments-container').append('<div class="text-center no-comment">No comments added yet</div>');
                        }
                    }
                    swal({
                        title: 'Deleted!',
                        text: 'Comment has been deleted.',
                        type: 'success',
                        confirmButtonColor: '#6777EF',
                        timer: 2000,
                    });
                },
                error: function (data) {
                    swal({
                        title: '',
                        text: data.responseJSON.message,
                        type: 'error',
                        confirmButtonColor: '#6777EF',
                        timer: 5000,
                    });
                },
            });
        });
});

$(".close").on('click', function () {
    $('#task_description').show();
    $('.task_edit_description').show();
    $('#task_no_description').show();
    $('.btnDescription').hide();
    TaskDescription.next().removeClass('border border-primary p-1 rounded').hide();
    $('.description_input').summernote({focus: false});
    $("#taskKanbanDetailsModel").modal('hide');
});

$(document).on('click', '.edit-comment', function () {
    let commentId = $(this).data('id');
    let commentData = $.trim($('.comments-data' + commentId).html());
    commentsNote.next().show();
    commentsNote.summernote('code', '');
    commentsNote.summernote('code', commentData);
    $('.comment-input').hide();
    $('#btnCommentSave').show();
    $('#btnCommentClose').show();
    $('#btnCommentSave').data('edit-mode', 1);
    $('#btnCommentSave').data('comment-id', commentId);
});

window.scrollToTheBottomAttachment = function () {
    setTimeout(function () {
        let height = $('#card-attachments-container').outerHeight();
        $('.attachments-content').scrollTop(height * height);
    }, 200);
};

document.addEventListener("DOMContentLoaded", init, false);

function init() {
    document.querySelector('#upload_attachment').addEventListener('change', handleFileSelect, false);
}
$('.btn-upload').hide();

function handleFileSelect(e){
    e.preventDefault();
    let files = e.target.files;
    let taskId = $('#taskId').val();
    if(files.length != 0) {
        // $('.choose-button').hide();
        // $('.btn-upload').show();
    }
    for(let i = 0; i < files.length; i++) {
        let ext = files[i].name.split('.').pop().toLowerCase();
        if ($.inArray(ext, [
            'png',
            'jpg',
            'jpeg',
            'xls',
            'xlsx',
            'csv',
            'pdf',
            'doc',
            'docx']) == -1) {
            displayErrorMessage(
                'The attachment must be a file of type: jpeg, jpg, png, xls, xlsx, pdf, doc');
            $(this).val('');
            // $('.choose-button').show();
            // $('.btn-upload').hide();
            return false;
        } else {
            if (files[i].size > 1000000) {
                displayErrorMessage(
                    'The attachment size should not greater than 12 mb.')
                $(this).val('')
                return false
            }
            let formData = new FormData()
            formData.append('file', files[i])
            $.ajax({
                url: route('task.add-attachment', taskId),
                type: 'post',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (result) {
                    let attachments = result.data;
                    if (result.success) {
                        // $('.btn-upload').hide();
                        // $('.choose-button').show();
                        $('#no_attachments').empty();
                        // for (let i = 0; attachments.total_files > i; i++) {
                        let ext = attachments.file_name.split('.').
                            pop();
                        let image = [];
                        let attachments_data = [];
                        if (ext === 'png' || 'jpg' || 'jpeg' || 'PNG' ||
                            'JPG') {
                            image = attachments.file_url;
                        }
                        if (ext === 'xlsx' || ext === 'xls' || ext === 'csv') {

                            image = '/assets/img/xls_icon.png';
                        }
                        if (ext === 'pdf') {
                            image = '/assets/img/pdf_icon.png';
                        }
                        if (ext === 'docx' || ext === 'doc') {
                            image = '/assets/img/doc_icon.png';
                        }

                        attachments_data = [
                            {
                                'id': attachments.id,
                                'url': attachments.file_url,
                                'image': image,
                                'username': attachments.user,
                                'downloadTask': route('tasks.index'),
                                'updated_at': moment(
                                    attachments.updated_at).
                                    fromNow(),
                                'createdId' : attachments.userId,
                                'loginUserId' : loggedInUserId,
                            }];
                        $('#attachments').append(prepareTemplateRender('#attachment', attachments_data));
                    }
                    // }
                    scrollToTheBottomAttachment();
                },
                error: function (result) {
                    manageAjaxErrors(result);
                },
                complete: function () {
                    // loadingButton.button('reset');
                    // $('.btn-upload').removeClass('disabled');
                },
            });
        }
    }
}

// $('#upload').submit(function (e){
//     e.preventDefault();
//     $('.btn-upload').addClass('disabled');
//     let formData = new FormData(this);
//     let taskId = $('#taskId').val();
//     let TotalFiles = $('#upload_attachment')[0].files.length; //Total files
//     let files = $('#upload_attachment')[0];
//     var loadingButton = jQuery(this).find('.btn-upload');
//     loadingButton.button('loading');
//     if(TotalFiles == 0){
//         displayErrorMessage('Please select a file');
//     }
//     for (let i = 0; i < TotalFiles; i++) {
//         formData.append('files' + i, files.files[i]);
//     }
//     formData.append('TotalFiles', TotalFiles);
//     $.ajax({
//         url:  route('task.add-attachment',taskId),,
//         type: 'post',
//         data: formData,
//         cache: false,
//         contentType: false,
//         processData: false,
//         dataType: 'json',
//         success: function (result) {
//             let attachments = result.data;
//             if (result.success) {
//                 $('.btn-upload').hide();
//                 $('.choose-button').show();
//                 $('#no_attachments').empty();
//                 for (let i = 0; attachments.total_files > i; i++) {
//                     let ext = attachments[i].attachment.file_name.split('.').pop();
//                     let image = [];
//                     let attachments_data = [];
//                     if (ext === 'png' || 'jpg' || 'jpeg' || 'PNG' || 'JPG'){
//                         image =  attachments[i].file_url;
//                     }
//                     if (ext === 'xlsx' || ext === 'xls' || ext === 'csv' ){
//
//                         image = '/assets/img/xls_icon.png';
//                     }
//                     if(ext === 'pdf'){
//                         image = '/assets/img/pdf_icon.png';
//                     }
//                     if(ext === 'docx' || ext === 'doc' ){
//                         image ='/assets/img/doc_icon.png';
//                     }
//                     attachments_data = [{
//                         'id' :  attachments[i].attachment.id,
//                         'url' :  attachments[i].file_url,
//                         'image' : image,
//                         'username' : attachments.user,
//                         'downloadTask': downloadTasks,
//                         'updated_at'  :   moment(attachments[i].attachment.updated_at).fromNow(),
//                     }];
//                     $('#attachments').append(prepareTemplateRender('#attachment',attachments_data));
//                 }
//             }
//             scrollToTheBottomAttachment();
//         },
//         error: function (result) {
//             manageAjaxErrors(result);
//         },
//         complete:function (){
//             loadingButton.button('reset');
//             $('.btn-upload').removeClass('disabled');
//         }
//     })
// });

$(document).on('click', '.delete-attachment', function () {
    let attachmentId = $(this).attr('data-id');
    let taskId = $('#taskId').val();
    let divId = $(this).parent().parent().parent().parent().prop('id');
    swal({
            title: deleteHeading + ' !',
            text: deleteMessage + ' "' + deleteAttachment + '" ?',
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#6777EF',
            cancelButtonColor: '#d33',
            cancelButtonText: noMessages,
            confirmButtonText: yesMessages,
        },
        function () {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
                },
                type: 'post',
                url: route('task.delete-attachment',attachmentId),
                data: { filename: name ,id : taskId},
                success: function () {
                    swal({
                        title: 'Deleted!',
                        text: 'Attachment has been deleted.',
                        type: 'success',
                        confirmButtonColor: '#6777EF',
                        timer: 2000
                    });

                    $('#'+divId+'').remove();
                    if(!$.trim($('#attachments').html()))
                    {
                        $('#no_attachments').append('No attachments added yet').addClass('text-center');
                    }
                },
                error: function (e) {
                    manageAjaxErrors();
                },
            });
        });
});

$('#taskKanbanDetailsModel').on('show.bs.modal',function (){
    $('#task_date_due').datetimepicker({
        format: 'YYYY-MM-DD',
        useCurrent: false,
        locale: languageName == 'ar' ? 'en' : languageName,
        icons: {
            previous: 'icon-arrow-left icons',
            next: 'icon-arrow-right icons',
        },
        sideBySide: true,
        minDate: moment().millisecond(0).second(0).minute(0).hour(0),
        widgetPositioning: {
            horizontal: 'right',
            vertical : 'bottom'
        },
    });

    $('#task_date_due').on('dp.show', function () {
        $("[data-toggle=popover]").popover('hide');
    });

    $('#status-data').popover({
        html: true,
        title: prepareTemplateRender('#status-popover'),
        content: function () {
            return $('#popover-content-status').html();
        },
    });

    $('#priority-data').popover({
        html: true,
        title: prepareTemplateRender('#priority-popover'),
        content: function () {
            return $('#popover-content-priority').html();
        },
    });
    $('#edit-task-assignee-data').popover({
        html: true,
        title: prepareTemplateRender('#assignee-popover'),
        content: function () {
            return $('#popover-content-edit-assignee').html();
        },
    });
});

$(document).on('click','#status',function (){
    $('#priority-data').popover('hide')
    $('#edit-task-assignee-data').popover('hide')
});

$(document).on('click','#priority',function (){
    $('#status-data').popover('hide')
    $('#edit-task-assignee-data').popover('hide')
});

$(document).on('click','#edit-task-assignee-data',function (){
    $('#priority-data').popover('hide')
    $('#status-data').popover('hide')
});

$(document).on('click','.btn-close-status',function (){
    $('#status-data').popover('hide')
});

$(document).on('click','.btn-close-priority',function (){
    $('#priority-data').popover('hide')
});

$(document).on('click','.btn-close-assignee',function (){
    $('#edit-task-assignee-data').popover('hide')
});

$(document).on('dp.change', '#task_date_due', function (e) {
    let date = $(this).val();
    let taskId = $('#taskId').val();
    $.ajax({
        url: route('kanban-task-details-update',taskId),
        type: 'post',
        data: { due_date: date },
        success: function (result) {
            $('#due_date').empty();
            if (result.success) {
                $('#due_date').append(prepareTemplateRender('#edit-due-date',[{'due_date' : moment(result.data.task.due_date).format('Do MMM, YYYY')}]));
            }
            $('#task_date_due').datetimepicker({
                format: 'YYYY-MM-DD',
                useCurrent: false,
                locale: languageName == 'ar' ? 'en' : languageName,
                icons: {
                    previous: 'icon-arrow-left icons',
                    next: 'icon-arrow-right icons',
                },
                sideBySide: true,
                minDate: moment().millisecond(0).second(0).minute(0).hour(0),
                widgetPositioning: {
                    horizontal: 'right',
                    vertical : 'bottom'
                },
            });
            $('#task_date_due').on('dp.show', function () {
                $("[data-toggle=popover]").popover('hide');
            });
        },
        error: function (result) {
            manageAjaxErrors(result);
        },
    });
});

$(document).on('click', '.status', function () {
    let status_id = $(this).attr('id');
    let taskId = $('#taskId').val();
    $('#status-data').popover('hide');
    $.ajax({
        url: route('kanban-task-details-update',taskId),
        type: 'post',
        data: { status: status_id },
        success: function (result) {
            $('#status').empty();
            if (result.success) {
                let status  = result.data.task.status_text;
                $('#status').append(prepareTemplateRender('#status-text',[{'status': status}]));
            }
            $('#status-data').popover({
                html: true,
                title: prepareTemplateRender('#status-popover'),
                content: function () {
                    return $('#popover-content-status').html();
                },
            });
        },
        error: function (result) {
            manageAjaxErrors(result);
        },
    });
});

$(document).on('click', '.priority', function () {
    let priority = $(this).attr('id');
    let taskId = $('#taskId').val();
    $('#priority-data').popover('hide');
    $.ajax({
        url: route('kanban-task-details-update',taskId),
        type: 'post',
        data: { priority: priority },
        success: function (result) {
            $('#priority').empty();
            if (result.success) {
                let priority  = result.data.task.priority;
                $('#priority').append(prepareTemplateRender('#priority-text',[{'priority': priority}]));
            }
            $('#priority-data').popover({
                html: true,
                title: prepareTemplateRender('#priority-popover'),
                content: function () {
                    return $('#popover-content-priority').html();
                },
            });
        },
        error: function (result) {
            manageAjaxErrors(result);
        },
    });
});

$(document).on('click', '.edit-assignee', function () {
    let user_id = $(this).attr('id');
    let taskId = $('#taskId').val();
    $('#edit-task-assignee-data').popover('hide');
    $.ajax({
        url: route('kanban-task-details-update',taskId),
        type: 'post',
        data: { user_id: user_id },
        success: function (result) {
            $('#task_assignee').empty();
            $('#popover-content-edit-assignee').empty();
            let assignee = result.data.task.task_assignee;
            let users = result.data.users;
            for (let i = 0; assignee.length > i; i++)
            {
                let assignee_data = [];
                assignee_data = [{
                    'name' :  assignee[i].name,
                    'avatar' : assignee[i].img_avatar,
                    'moreAssignee' : assignee.length - 6,
                }];
                if(i < 6){
                    $('#task_assignee').append(prepareTemplateRender('#task-assignee',assignee_data));
                }else if(i == (assignee.length - 1)){
                    $('#task_assignee').append(prepareTemplateRender('#more-assignee',assignee_data));
                }
            }
            if(users != ''){
                $('#task_assignee').append(prepareTemplateRender('#add-assignee',[{'id' : result.data.id}]));
            }
            $.each(users,function (i,val){
                $('#popover-content-edit-assignee').append('<div href="#" class="edit-assignee popover-div pl-1" id="'+ i +'"><span>'+val+'</span></div>')
            });
            $('#edit-task-assignee-data').popover({
                html: true,
                title: prepareTemplateRender('#assignee-popover'),
                content: function () {
                    return $('#popover-content-edit-assignee').html();
                },
            });
        },
        error: function (result) {
            manageAjaxErrors(result);
        },
    });
});

const renderTaskDetails = (data) => {
    let model = $('#taskDetails');
    model.find('#taskId').val(data.id);
    model.find('#title').val(data.title);
    model.find('#dueDate').val(data.due_date);
    let element = document.createElement('textarea');
    element.innerHTML = (!isEmpty(data.description))
        ? data.description
        : 'N/A';
    model.find('#taskDescription').summernote('code',element.value);
}

$(document).on('click', '.task-details', function (e) {
    e.preventDefault();
    $('#taskDetails').find('.infy-loader').show();
    $('#taskDetails').modal('show');
    let id = $(this).data('id');
    $.ajax({
        url: route('task.kanban-edit',id),
        type: 'get',
        success: function (result) {
            if (result.success) {
                renderTaskDetails(result.data);
                $('#taskDetails').find('.infy-loader').fadeOut();
            }
        },
        error: function (result) {
            manageAjaxErrors(result);
        },
    })
});

$('#taskDetails').on('hidden.bs.modal', function () {
    $(this).find('#title').val('');
    $(this).find('#dueDate').val('');
    $(this).find('#taskDescription').summernote('code', '');
    $(this).find('#taskDescription').val('');
});

$(document).on('submit', '#taskDetailsForm', function (e) {
    e.preventDefault();
    let id = $(this).find('#taskId').val();
    let form = $(this);
    let formData = $(this).serializeArray();
    let loadingButton = jQuery(this).find('#btnTaskSave');
    loadingButton.button('loading');
    $.ajax({
        url: route('task.kanban-edit',id),
        type: 'post',
        data: formData,
        success: function (result) {
            if (result.success) {
                location.reload(true);
            }
        },
        error: function (result) {
            manageAjaxErrors(result)
        }
    })
});

$(document).ready(function () {
    $('#taskDescription').summernote({
        placeholder: 'Add Task description...',
        minHeight: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['para', ['paragraph']]],
    });
})

jQuery(document).ready(function(){
    if( $('.cd-stretchy-nav').length > 0 ) {
        var stretchyNavs = $('.cd-stretchy-nav');

        stretchyNavs.each(function(){
            var stretchyNav = $(this),
                stretchyNavTrigger = stretchyNav.find('.cd-nav-trigger');

            stretchyNavTrigger.on('click', function(event){
                event.preventDefault();
                stretchyNav.toggleClass('nav-is-visible');
            });
        });

        $(document).on('click', function(event){
            ( !$(event.target).is('.cd-nav-trigger') && !$(event.target).is('.cd-nav-trigger span') ) && stretchyNavs.removeClass('nav-is-visible');
        });
    }
});

$('.subdropdown p').on('click', function (e) {
    $(this).parent('.dropdown').dropdown('toggle');
    e.stopPropagation();
    e.preventDefault();
})

$(document).ready(() => {
    $('#projectDropdown').select2();

    $('#usersDropdown').select2();

    setTimeout(function () {
        $('#projectDropdown').trigger('change');
    }, 100);

    $(document).on('change', '#projectDropdown', function () {
        $('#usersDropdown').empty();
        let id = $(this).val();
        $.ajax({
            url: route('users-by-project',id),
            type: 'GET',
            success: function (result) {
                if (result.success) {
                    const users = result.data;
                    $('#usersDropdown').
                        find('option').
                        remove().
                        end().
                        append('<option value="">All</option>');
                    for (const key in users) {
                        if (users.hasOwnProperty(key)) {
                            $('#usersDropdown').
                                append($('<option>',
                                    { value: users[key], text: key }));
                        }
                    }
                }
            },
            error: function (result) {
                if ($('#projectDropdown').val() == null) {
                    displayErrorMessage('User not have any projects');
                } else {
                    manageAjaxErrors(result);
                }
            },
        });
        window.livewire.emit('loadByProject', $(this).val());
    });

    if (!loginUserRole) {
        $('#usersDropdown').val(authUserId).trigger('change');
        window.livewire.emit('loadByUser', $('#loginUserId').val());
    } else {
        $(document).on('change', '#usersDropdown', function () {
            window.livewire.emit('loadByUser', $(this).val());
        });
    }
});

document.addEventListener('livewire:load', function () {
    window.livewire.hook('message.processed', () => {
        setTimeout(function () {
            $(document).find('[data-toggle="tooltip"]').tooltip('dispose')
            $(document).find('[data-toggle="tooltip"]').tooltip()
        }, 100)
    })
})

window.scrollToTheBottomAttachment = function () {
    setTimeout(function () {
        let height = $('#card-attachments-container-edit').outerHeight()
        $('.attachments-content-edit').scrollTop(height * height)
    }, 200)
}
