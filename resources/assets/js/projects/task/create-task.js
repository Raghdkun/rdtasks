'use strict'
$(document).ready(function () {
    $('#taskPriority').select2({
        width: '100%',
    })
    $('#taskAssignee').select2({
        width: '100%',
        placeholder: 'Select Assignee',
    })
    if (canManageTags) {
        $('#taskTagIds').select2({
            width: '100%',
            tags: true,
            placeholder: 'Select Tags',
            createTag: function (tag) {
                let found = false
                $('#taskTagIds option').each(function () {
                    if ($.trim(tag.term).toUpperCase() ===
                        $.trim($(this).text()).toUpperCase()) {
                        found = true
                    }
                })
                if (!found) {
                    return {
                        id: tag.term,
                        text: tag.term,
                    }
                }
            },
        })
    } else {
        $('#taskTagIds').select2({
            width: '100%',
            placeholder: 'Select Tags',
        })
    }
})
let taskAssignees = [];

$('#taskEstimateTimeHours').datetimepicker({
    format: 'HH:mm',
    useCurrent: false,
    locale: languageName == 'ar' ? 'en' : languageName,
    icons: {
        previous: 'icon-arrow-left icons',
        next: 'icon-arrow-right icons',
        up: 'icon-arrow-up icons',
        down: 'icon-arrow-down icons',
    },
    sideBySide: true,
})

$('#taskEstimateTimeDays').hide()

$(document).on('click', '#taskHours', function () {
    $('#taskDays').removeClass('btn btn-primary text-white')
    $(this).addClass('btn btn-primary text-white')
    $('#taskTypes').val(0)
    $('#taskEstimateTimeDays').hide().val('')
    $('#taskEstimateTimeHours').show()
})

$(document).on('click', '#taskDays', function () {
    $('#taskHours').removeClass('btn btn-primary text-white')
    $(this).addClass('btn btn-primary text-white')
    $('#taskTypes').val(1)
    $('#taskEstimateTimeHours').data('DateTimePicker').date('00:00')
    $('#taskEstimateTimeHours').hide().val('')
    $('#taskEstimateTimeDays').show()
})
$('#taskEstimateTimeHours').datetimepicker({
    format: 'HH:mm',
    useCurrent: false,
    locale: languageName == 'ar' ? 'en' : languageName,
    icons: {
        previous: 'icon-arrow-left icons',
        next: 'icon-arrow-right icons',
        up: 'icon-arrow-up icons',
        down: 'icon-arrow-down icons',
    },
    sideBySide: true,
})

$('#taskDueDate').datetimepicker({
    format: 'YYYY-MM-DD',
    useCurrent: false,
    locale: languageName == 'ar' ? 'en' : languageName,
    icons: {
        previous: 'icon-arrow-left icons',
        next: 'icon-arrow-right icons',
    },
    sideBySide: true,
    minDate: moment().millisecond(0).second(0).minute(0).hour(0),
})

$('#taskDescription').summernote({
    placeholder: 'Add Task description...',
    minHeight: 200,
    toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough']],
        ['para', ['paragraph']]],
});

let projectId = $('#projectId').val();
loadProjectAssignees(projectId, 'taskAssignee');

function loadProjectAssignees (projectId, selector) {
    taskAssignees = [];
    $('#' + selector).empty();
    $('#' + selector).trigger('change')
    $.ajax({
        url: route('users-of-projects') + '?projectIds=' + projectId,
        type: 'GET',
        success: function (result) {
            const users = result.data;
            for (const key in users) {
                if (users.hasOwnProperty(key)) {
                    $('#' + selector).
                        append($('<option>', { value: key, text: users[key] }));
                    taskAssignees.push(key);
                }
            }
            // condition applied only when new task modal is opened
            if (($('#projectId').val() !== '') && !isShow) {
                $('#' + selector).val(currentLoggedInUserId);
                $('#' + selector).trigger('change.select2');
            }
        },
    })
}

$(document).on('submit', '#addTaskNewForm', function (event) {
    event.preventDefault()
    let $description = $('<div />').
        html($('#taskDescription').summernote('code'))
    let empty = $description.text().trim().replace(/ \r\n\t/g, '') === ''
    let loadingButton = jQuery(this).find('#btnTaskSave')
    loadingButton.button('loading')

    if ($('#taskDescription').summernote('isEmpty')) {
        $('#taskDescription').val('')
    } else if (empty) {
        displayErrorMessage(
            'Description field is not contain only white space')
        loadingButton.button('reset')
        return false
    }

    let formData = new FormData($(this)[0])

    let files = $('#Add_attachment')[0].files
    for (let i = 0; i < files.length; i++) {
        formData.append('file[]', files[i])
    }
    $.ajax({
        url: route('tasks.store'),
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message)
                $('#addTaskModal').modal('hide')
                $('#task_table').DataTable().ajax.reload()
                revokerTracker()
                window.livewire.emit('refresh')
            }
        },
        error: function (result) {
            printErrorMessage('#projectTaskValidationErrorsBox', result)
        },
        complete: function () {
            loadingButton.button('reset')
        },
    })
})

$('#addTaskModal').on('hidden.bs.modal', function () {
    $('#taskDescription').summernote('code', '')
    $('#taskAssignee').val(null).trigger('change')
    $('#taskTagIds').val(null).trigger('change')
    $('#taskPriority').val(null).trigger('change')
    $('#taskDueDate').data('DateTimePicker').clear()
    $('#taskEstimateTimeHours').data('DateTimePicker').date('00:00')
    $('#taskDays').removeClass('btn btn-primary text-white')
    $('#taskHours').addClass('btn btn-primary text-white')
    $('#taskTypes').val(0)
    $('#taskEstimateTimeDays').hide().val('')
    $('#taskEstimateTimeHours').show().val('')
    $('#previewImage').empty()
    resetModalForm('#addTaskNewForm', '#validationErrorsBox')
})

//
// $(document).on('click', '.task-details', function (e) {
//     e.preventDefault();
//     let curEle = $(this);
//     $(this).addClass('disabled');
//     let taskId = $(this).attr('data-id');
//     $.ajax({
//         url: route('kanban-task-details',taskId),
//         type: 'get',
//         success: function (result) {
//             if (result.success) {
//                 renderTaskDetailsKanban(result.data);
//                 $('#taskKanbanDetailsModel').modal('show');
//                 scrollToTheBottomComment();
//             }
//         },
//         error: function (result) {
//             manageAjaxErrors(result)
//         },
//         complete:function (){
//             curEle.removeClass('disabled');
//         }
//     })
// });
//
// const renderTaskDetailsKanban = (data) => {
//     let tasks_details = data.taskDetails;
//     let status = data.taskStatus;
//     let priority = data.priority;
//     let users = data.users;
//     let image = [] ,comments = [] ,attachments = [];
//     image.push(tasks_details.task_assignee);
//     comments.push(tasks_details.comments);
//     attachments.push(data.attachments);
//     let loginUser = $('#userId').val();
//
//     $('#taskId').val(tasks_details.id);
//     $('#task_title').empty();
//     $('#task_title').append(tasks_details.title);
//     $('#task_timer_card_16,#task_timer_all_card_16').
//         append(roundToQuarterHourAll(tasks_details.task_total_minutes));
//     (!isEmpty(tasks_details.description)) ? $('#task_description').
//         append(tasks_details.description) : $('#task_no_description').
//         append('No Description Added Yet');
//     $('#start_date').
//         append(moment(tasks_details.created_at).format('Do MMM, YYYY'));
//     $('#status').
//         append(prepareTemplateRender('#status-text',
//             [{ 'status': tasks_details.status_text }]));
//     if (!isEmpty(tasks_details.priority)) {
//         $('#priority').
//             append(prepareTemplateRender('#priority-text',
//                 [{ 'priority': tasks_details.priority }]));
//     } else {
//         $('#priority').
//             append(prepareTemplateRender('#priority-text',
//                 [{ 'priority': 'N/A' }]));
//     }
//     $('#created_by').append(tasks_details.created_user.name);
//     $('#created_date').
//         append(moment(tasks_details.created_at).format('DD-MM-YYYY'));
//     $('#project_id').
//         append('#' + tasks_details.project.name).
//         attr('href', 'user-assign-projects/' + tasks_details.project.id);
//     $('#project_admin_id').
//         append('#' + tasks_details.project.name).
//         attr('href', 'projects/' + tasks_details.project.id);
//     if (!isEmpty(tasks_details.due_date)) {
//         $('#due_date').
//             append(prepareTemplateRender('#edit-due-date', [
//                 {
//                     'due_date': moment(tasks_details.due_date).
//                         format('Do MMM, YYYY'),
//                 }]));
//     } else {
//         $('#due_date').
//             append(prepareTemplateRender('#edit-due-date',
//                 [{ 'due_date': moment(moment()).format('Do MMM, YYYY') }]));
//     }
//     // $('#popover-content-status').
//     //     append(prepareTemplateRender('#status-popover'));
//     // $('#popover-content-priority').append(prepareTemplateRender('#priority-popover'));
//     // $('#popover-content-edit-assignee').append(prepareTemplateRender('#assignee-popover'));
//
//     $.each(status, function (i, val) {
//         $('#popover-content-status').
//             append(
//                 '<div class="status popover-div cursor-pointer pl-2" id="' + i +
//                 '"><span>' + val + '</span></div>');
//     });
//     $.each(priority, function (i, val) {
//         $('#popover-content-priority').
//             append(
//                 '<div class="priority popover-div cursor-pointer pl-2" id="' +
//                 i + '"><span>' + val + '</span></div>');
//     });
//     $.each(users, function (i, val) {
//         $('#popover-content-edit-assignee').
//             append(
//                 '<div class="edit-assignee popover-div cursor-pointer pl-1" id="' +
//                 i + '"><span>' + val + '</span></div>');
//     });
//     if(data.attachments.length !== 0 ) {
//         for (let i = 0; attachments[0].length > i; i++) {
//             let ext = data.attachments[i].name.split('.').pop();
//             let image = [];
//             let attachments_data = [];
//             if (ext === 'png' || 'jpg' || 'jpeg' || 'PNG' || 'JPG'){
//                 image = data.attachments[i].url;
//             }
//             if (ext === 'xlsx' || ext === 'xls' || ext === 'csv' ){
//
//                 image = '/assets/img/xls_icon.png';
//             }
//             if(ext === 'pdf'){
//                 image = '/assets/img/pdf_icon.png';
//             }
//             if(ext === 'docx' || ext === 'doc' ){
//                 image ='/assets/img/doc_icon.png';
//             }
//             attachments_data = [{
//                 'id' : data.attachments[i].id,
//                 'url' : data.attachments[i].url,
//                 'image' : image,
//                 'username' : tasks_details.created_user.name,
//                 'downloadTask': route('tasks.index'),
//                 'updated_at'  :  moment(tasks_details.attachments[i].updated_at).fromNow(),
//                 'createdId' : tasks_details.created_user.id,
//                 'loginUserId' : loggedInUserId,
//             }];
//             $('#attachments').append(prepareTemplateRender('#attachment',attachments_data));
//         }
//     }else {
//         $('#no_attachments').append('No attachments added yet').addClass('text-center');
//     }
//     if(tasks_details.comments.length !== 0 ){
//         for (let i = 0; comments[0].length > i; i++) {
//             let comment_data = [];
//             comment_data = [{
//                 'id' :  tasks_details.comments[i].id,
//                 'userImage' : tasks_details.comments[i].created_user.img_avatar,
//                 'userName' : tasks_details.comments[i].created_user.name,
//                 'comment' : tasks_details.comments[i].comment,
//                 'updated_at' :  moment(tasks_details.comments[i].updated_at).fromNow(),
//                 'userId' : tasks_details.comments[i].created_user.id,
//                 'loginUserId' : loginUser,
//             }];
//             $('#card-comments-container').append(prepareTemplateRender('#comment',comment_data));
//         }
//     }else {
//         $('#card-comments-container').append('<div class="text-center no-comment">No comments added yet</div>');
//     }
//     for (let i = 0; image[0].length > i; i++)
//     {
//         let assignee_data = [];
//         assignee_data = [{
//             'name' :  tasks_details.task_assignee[i].name,
//             'avatar' : tasks_details.task_assignee[i].img_avatar,
//             'moreAssignee' : image[0].length - 6,
//         }];
//         if(i < 6){
//             $('#task_assignee').append(prepareTemplateRender('#task-assignee',assignee_data));
//         }else if(i == (image[0].length - 1)){
//             $('#task_assignee').append(prepareTemplateRender('#more-assignee',assignee_data));
//         }
//     }
//     if(users != ''){
//         $('#task_assignee').append(prepareTemplateRender('#add-assignee',[{'id' : tasks_details.id}]));
//     }
// }
//
// window.scrollToTheBottomComment = function () {
//     setTimeout(function () {
//         let height = $('#card-comments-container').outerHeight();
//         $('.comment-content').scrollTop(height * height);
//     }, 200);
// };
// $(document).on('click', '#btnCommentSave', function () {
//     const loadingButton = jQuery(this);
//     loadingButton.button('loading');
//     let curEle = $(this);
//     $(this).addClass('disabled');
//     let comment = commentsNote.summernote('code');
//     let $description = $('<div />').html($('#comments-input').summernote('code'));
//     let empty = $description.text().trim().replace(/ \r\n\t/g, '') === '';
//     let taskId = $('#taskId').val();
//     let commentId = $(this).data('comment-id');
//     const editMode = $(this).data('edit-mode');
//     let commentUrl = null;
//     let loginUser = $('#userId').val();
//
//     if (commentsNote.summernote('isEmpty')) {
//         displayErrorMessage('Please Enter comment');
//         curEle.removeClass('disabled');
//         loadingButton.button('reset');
//         return false;
//     }else if (empty){
//         displayErrorMessage('Comment is not contain only white space');
//         curEle.removeClass('disabled');
//         loadingButton.button('reset');
//         return false;
//     }
//     if (editMode === 0)
//         commentUrl = route('task.comments',taskId);
//     else
//         commentUrl =  route('task.update-comment',[taskId,commentId]);
//     $.ajax({
//         url: commentUrl,
//         type: 'post',
//         data: { 'comment': comment },
//         success: function (result) {
//             if (result.success) {
//                 commentsNote.next().hide();
//                 $('#btnCommentSave').hide();
//                 $('#btnCommentClose').hide();
//                 $('.no-comment').remove();
//                 $('.comment-input').show();
//                 commentsNote.summernote('code','');
//                 if (editMode === 0){
//                     let comments = result.data.comment;
//                     let comment_data = [];
//                     comment_data = [{
//                         'id' :  comments.id,
//                         'userImage' : comments.created_user.img_avatar,
//                         'userName' : comments.created_user.name,
//                         'comment' : comments.comment,
//                         'updated_at' :  moment(comments.updated_at).fromNow(),
//                         'userId' : comments.created_user.id,
//                         'loginUserId' : loginUser,
//                     }];
//                     scrollToTheBottomComment();
//                     $('#card-comments-container').append(prepareTemplateRender('#comment',comment_data));
//                 }
//                 else {
//                     $('.comments-data' + commentId).html(comment);
//                     $('#btnCommentSave').data('edit-mode', 0);
//                     $('#btnCommentSave').data('comment-id', 0);
//                 }
//             }
//         },
//         error: function (result) {
//             printErrorMessage('#taskValidationErrorsBox', result);
//         },
//         complete:function (){
//             curEle.removeClass('disabled');
//             loadingButton.button('reset');
//         }
//     });
// });
//
// $(document).on('click', '.delete-comment', function () {
//     let commentId = $(this).data('id');
//     let taskId = $('#taskId').val();
//     let divId = $(this).parent().parent().parent().parent().parent().parent().prop('id');
//     swal({
//             title: 'Delete !',
//             text: 'Are you sure want to delete this "Comment" ?',
//             type: 'warning',
//             showCancelButton: true,
//             closeOnConfirm: false,
//             showLoaderOnConfirm: true,
//             confirmButtonColor: '#6777EF',
//             cancelButtonColor: '#d33',
//             cancelButtonText: 'No',
//             confirmButtonText: 'Yes',
//         },
//         function () {
//             $.ajax({
//                 url: route('task.delete-comment',[taskId,commentId]),
//                 type: 'DELETE',
//                 success: function (result) {
//                     if (result.success) {
//                         $('#'+divId+'').remove();
//                         commentsNote.summernote('code', '');
//                         $('#btnCommentSave').data('edit-mode', 0);
//                         $('#btnCommentSave').data('comment-id', 0);
//                         if(!$.trim($('#card-comments-container').html()))
//                         {
//                             $('#card-comments-container').append('<div class="text-center no-comment">No comments added yet</div>');
//                         }
//                     }
//                     swal({
//                         title: 'Deleted!',
//                         text: 'Comment has been deleted.',
//                         type: 'success',
//                         confirmButtonColor: '#6777EF',
//                         timer: 2000,
//                     });
//                 },
//                 error: function (data) {
//                     swal({
//                         title: '',
//                         text: data.responseJSON.message,
//                         type: 'error',
//                         confirmButtonColor: '#6777EF',
//                         timer: 5000,
//                     });
//                 },
//             });
//         });
// });
// $('.btn-upload').hide();
// let commentsNote = $('#comments-input').summernote({
//     placeholder: 'Add Comment...',
//     minHeight: 200,
//     toolbar: [
//         ['style', ['bold', 'italic', 'underline', 'clear']],
//         ['font', ['strikethrough']],
//         ['para', ['paragraph']]],
// });
// commentsNote.next().hide();
// $('#btnCommentSave').hide();
// $('#btnCommentClose').hide();
// $(document).on('click', '.comment-input', function () {
//     $(this).hide();
//     commentsNote.next().addClass('border border-primary p-1 rounded').show();
//     $('#comments-input').summernote({focus:true});
//     $('#btnCommentSave').show();
//     $('#btnCommentClose').show();
// });
// $('#taskKanbanDetailsModel').on('hidden.bs.modal', function () {
//     $(this).find('#task_title,#task_assignee,#task_description,#task_timer_card_16,#task_timer_all_card_16,#task_assignee,#start_date,#due_date,#status,#priority,#task_id,#created_by,#created_date,#project_id,#project_admin_id,#card-comments-container,#attachments,#task_no_description,#no_attachments,#popover-content-status,#popover-content-priority,#popover-content-edit-assignee').empty();
//     $('.btn-upload').hide();
//     $('.choose-button').show();
//     commentsNote.next().hide();
//     $('#btnCommentSave').hide();
//     $('#btnCommentClose').hide();
//     $('.comment-input').show();
//     window.livewire.emit('refresh');
// });
// document.addEventListener("DOMContentLoaded", init, false);
//
// function init() {
//     document.querySelector('#upload_attachment').addEventListener('change', handleFileSelect, false);
// }
// $('.btn-upload').hide();
//
// function handleFileSelect(e){
//     e.preventDefault();
//     let files = e.target.files;
//     let taskId = $('#taskId').val();
//     console.log(taskId)
//     if(files.length != 0) {
//         // $('.choose-button').hide();
//         // $('.btn-upload').show();
//     }
//     for(let i = 0; i < files.length; i++) {
//         let ext = files[i].name.split('.').pop().toLowerCase();
//         if ($.inArray(ext, [
//             'png',
//             'jpg',
//             'jpeg',
//             'xls',
//             'xlsx',
//             'csv',
//             'pdf',
//             'doc',
//             'docx']) == -1) {
//             displayErrorMessage(
//                 'The attachment must be a file of type: jpeg, jpg, png, xls, xlsx, pdf, doc');
//             $(this).val('');
//             // $('.choose-button').show();
//             // $('.btn-upload').hide();
//             return false;
//         } else {
//             let formData = new FormData();
//             formData.append('file', files[i]);
//
//             $.ajax({
//                 url: route('task.add-attachment',taskId),
//                 type: 'post',
//                 data: formData,
//                 cache: false,
//                 contentType: false,
//                 processData: false,
//                 dataType: 'json',
//                 success: function (result) {
//                     let attachments = result.data;
//                     if (result.success) {
//                         // $('.btn-upload').hide();
//                         // $('.choose-button').show();
//                         $('#no_attachments').empty();
//                         // for (let i = 0; attachments.total_files > i; i++) {
//                         let ext = attachments.file_name.split('.').
//                             pop();
//                         let image = [];
//                         let attachments_data = [];
//                         if (ext === 'png' || 'jpg' || 'jpeg' || 'PNG' ||
//                             'JPG') {
//                             image = attachments.file_url;
//                         }
//                         if (ext === 'xlsx' || ext === 'xls' || ext === 'csv') {
//
//                             image = '/assets/img/xls_icon.png';
//                         }
//                         if (ext === 'pdf') {
//                             image = '/assets/img/pdf_icon.png';
//                         }
//                         if (ext === 'docx' || ext === 'doc') {
//                             image = '/assets/img/doc_icon.png';
//                         }
//
//                         attachments_data = [
//                             {
//                                 'id': attachments.id,
//                                 'url': attachments.file_url,
//                                 'image': image,
//                                 'username': attachments.user,
//                                 'downloadTask': route('tasks.index'),
//                                 'updated_at': moment(
//                                     attachments.updated_at).
//                                     fromNow(),
//                                 'createdId' : attachments.userId,
//                                 'loginUserId' : loggedInUserId,
//                             }];
//                         $('#attachments').append(prepareTemplateRender('#attachment', attachments_data));
//                     }
//                     // }
//                     scrollToTheBottomAttachment();
//                 },
//                 error: function (result) {
//                     manageAjaxErrors(result);
//                 },
//                 complete: function () {
//                     // loadingButton.button('reset');
//                     // $('.btn-upload').removeClass('disabled');
//                 },
//             });
//         }
//     }
// }
// $(document).on('click', '.delete-attachment', function () {
//     let attachmentId = $(this).attr('data-id');
//     let taskId = $('#taskId').val();
//     let divId = $(this).parent().parent().parent().parent().prop('id');
//     swal({
//             title: deleteHeading + ' !',
//             text: deleteMessage + ' "' + deleteAttachment + '" ?',
//             type: 'warning',
//             showCancelButton: true,
//             closeOnConfirm: false,
//             showLoaderOnConfirm: true,
//             confirmButtonColor: '#6777EF',
//             cancelButtonColor: '#d33',
//             cancelButtonText: noMessages,
//             confirmButtonText: yesMessages,
//         },
//         function () {
//             $.ajax({
//                 headers: {
//                     'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
//                 },
//                 type: 'post',
//                 url: route('task.delete-attachment',attachmentId),
//                 data: { filename: name ,id : taskId},
//                 success: function () {
//                     swal({
//                         title: 'Deleted!',
//                         text: 'Attachment has been deleted.',
//                         type: 'success',
//                         confirmButtonColor: '#6777EF',
//                         timer: 2000
//                     });
//
//                     $('#'+divId+'').remove();
//                     if(!$.trim($('#attachments').html()))
//                     {
//                         $('#no_attachments').append('No attachments added yet').addClass('text-center');
//                     }
//                 },
//                 error: function (e) {
//                     manageAjaxErrors();
//                 },
//             });
//         });
// });
