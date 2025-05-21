'use strict';

$(document).ready(function () {
    $('#projectStatus').select2({
        width: '100%',
    });
});

$(document).on('change', '#projectStatus', function () {
    let projectStatus = $(this).val();
    projectStatusLivewire(projectStatus);
});

window.projectStatusLivewire = function ($projectStatus) {
    window.livewire.emit('projectsStatus', $projectStatus);
};
document.addEventListener('livewire:load', function () {
    window.livewire.hook('message.processed', () => {
        $('#projectStatus').select2({
            width: '100%',
        })
    });
});
//popover on more users  of my project detail screen
$('[data-toggle=popover]').popover({
    html: true,
    content: function () {
        return $('#popover-content').html();
    },
});

$(function () {
    $('#editAssignTo').select2({
        width: '100%',
        placeholder: 'Select Assignee',
    });
    $('#editProjectId').select2({
        width: '100%',
        placeholder: 'Select Project',
    });
    $('#editStatus').select2({
        width: '100%',
    });
    $('#editAssignee').select2({
        width: '100%',
        placeholder: 'Select Assignee',
    });

    if (canManageTags) {
        $('#editTagIds').select2({
            width: '100%',
            tags: true,
            placeholder: 'Select Tags',
            createTag: function (tag) {
                var found = false;
                $('#editTagIds option').each(function () {
                    if ($.trim(tag.term).toUpperCase() ===
                        $.trim($(this).text()).toUpperCase()) {
                        found = true;
                    }
                });
                if (!found) {
                    return {
                        id: tag.term,
                        text: tag.term,
                    };
                }
            },
        });
    } else {
        $('#editTagIds').select2({
            width: '100%',
            placeholder: 'Select Tags',
        });
    }
    $('#editPriority').select2({
        width: '100%',
        placeholder: 'Select Priority',
    });

    $('#dueDate,#editDueDate').datetimepicker({
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

    $('#dueDate, #editDueDate').on('dp.show', function () {
        matchWindowScreenPixels({
            dueDate: '#dueDate',
            editDueDate: '#editDueDate',
        }, 'tsk');
    });

    $(window).resize(function () {
        matchWindowScreenPixels({
            dueDate: '#dueDate',
            editDueDate: '#editDueDate',
        }, 'tsk');
    }).trigger('resize');

    $('[data-toggle="tooltip"]').tooltip();

});
let quillTask = $('#taskEditDescription').summernote({
    placeholder: 'Add Task description...',
    minHeight: 200,
    toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough']],
        ['para', ['paragraph']]],
});

let taskAssignees = [];

$(document).on('click', '.edit-task-btn', function (event) {
    let id = $(event.currentTarget).attr('data-id');
    $.ajax({
        url: route('tasks.edit',id),
        type: 'GET',
        success: function (result) {
            if (result.success) {
                let task = result.data.task;
                let allTags = result.data.tags;
                let elementTag = document.createElement('textarea');
                $('#editTagIds').empty();
                $.each(allTags, function (i, e) {
                    elementTag.innerHTML = e;
                    $('#editTagIds').
                        append($('<option>',
                            { value: i, text: elementTag.value }));
                });

                let desc = task.description;
                let element = document.createElement('textarea');
                element.innerHTML = task.title;
                $('#tagId').val(task.id);
                $('#editTitle').val(element.value);
                $('#taskEditDescription').summernote('code', task.description);
                $('#editDueDate').val(task.due_date);
                $('#editProjectId').val(task.project.id).trigger('change');
                $('#editStatus').val(task.status).trigger('change');
                if (task.estimate_time_type == null && task.estimate_time ==
                    null) {
                    $('#editDays').removeClass('btn-primary text-white');
                }
                if (task.estimate_time_type == 0 || task.estimate_time ==
                    null) {
                    $('#editEstimateTimeDays').hide();
                    $('#editEstimateTimeHours').val(task.estimate_time).show();
                    $('#editHours').addClass('btn-primary text-white');
                    $('#editTypes').val(0);
                } else {
                    $('#editEstimateTimeHours').hide();
                    $('#editEstimateTimeDays').val(task.estimate_time).show();
                    $('#editDays').addClass('btn-primary text-white');
                    $('#editTypes').val(1);
                }
                let tagsIds = [];
                let userIds = [];
                $(task.tags).each(function (i, e) {
                    tagsIds.push(e.id);
                });
                $(task.task_assignee).each(function (i, e) {
                    userIds.push(e.id);
                    taskAssignees.push(e.id);
                });
                $('#editTagIds').val(tagsIds).trigger('change');

                $('#editAssignee').val(userIds).trigger('change');
                $('#editPriority').val(task.priority).trigger('change');

                setTimeout(function () {
                    $.each(task.task_assignee, function (i, e) {
                        $('#editAssignee option[value=\'' + e.id + '\']').
                            prop('selected', true).
                            trigger('change');
                    });
                    $('#EditModal').appendTo('body').modal('show');

                }, 1500);
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
    });
});

$(document).on('change', '#editProjectId', function (event) {
    let projectId = $(this).val();
    loadProjectAssignees(projectId, 'editAssignee');
});

function loadProjectAssignees (projectId, selector) {
    $('#' + selector).empty();
    $('#' + selector).trigger('change');
    $.ajax({
        url: route('users-of-projects') + '?projectIds=' + projectId,
        type: 'GET',
        success: function (result) {
            const users = result.data;
            for (const key in users) {
                if (users.hasOwnProperty(key)) {
                    $('#' + selector).
                        append(
                            $('<option>', { value: key, text: users[key] }));
                }
            }
        },
    });
}

$(document).on('submit', '#editForm', function (event) {
    event.preventDefault();
    let $description = $('<div />').
        html($('#taskEditDescription').summernote('code'));
    let empty = $description.text().trim().replace(/ \r\n\t/g, '') === '';
    var loadingButton = jQuery(this).find('#btnTaskEditSave');
    loadingButton.button('loading');

    if ($('#taskEditDescription').summernote('isEmpty')) {
        $('#taskEditDescription').val('');
    } else if (empty) {
        displayErrorMessage(
            'Description field is not contain only white space');
        loadingButton.button('reset');
        return false;
    }
    if ($('#editTitle').val().trim() == '') {
        displayErrorMessage('Title field is not contain only white space');
        loadingButton.button('reset');
        return false;
    }
    var id = $('#tagId').val();
    let stopwatchTaskId = getItemFromLocalStorage('task_id');
    let isClockRunning = getItemFromLocalStorage('clockRunning');
    if (id === stopwatchTaskId && isClockRunning === 'true') {
        swal({
            'title': 'Warning',
            'text': 'Please stop timer before updating task.',
            'type': 'warning',
            confirmButtonColor: '#6777EF',
        });
        loadingButton.button('reset');
        return false;
    }
    let formdata = $(this).serializeArray();
    let desc = quillTask.summernote('code');  // retrieve the HTML content from the Summernote container

    $.ajax({
        url: route('tasks.update',id),
        type: 'put',
        data: formdata,
        success: function (result) {
            if (result.success) {
                $('#EditModal').modal('hide');
                window.livewire.emit('refresh');
                loadingButton.button('reset');
                displaySuccessMessage('Task updated successfully.');
            }
        },
        error: function (result) {
            loadingButton.button('reset');
            printErrorMessage('#editValidationErrorsBox', result);
        },
    });
});

$('#EditModal').on('hidden.bs.modal', function () {
    // to empty content of the Summernote Editor instance/container
    quillTask.summernote('code', '');
    resetModalForm('#editForm', '#editValidationErrorsBox');
});

$(document).ready(function () {
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab', $(e.target).attr('href'));
    });
    var activeTab = localStorage.getItem('activeTab');

    if (activeTab) {
        $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
        localStorage.removeItem('activeTab');
    }
});

let updateTaskStatus;
let currentCheckbox;

$(function () {

    $(document).on('change', 'input.complete-task-checkbox', function (e) {
        let taskId = ($(this).attr('data-check'));
        currentCheckbox = $(this);
        let stopwatchTaskId = getItemFromLocalStorage('task_id');
        let isClockRunning = getItemFromLocalStorage('clockRunning');
        if (taskId === stopwatchTaskId && isClockRunning === 'true') {
            swal({
                'title': 'Warning',
                'text': 'Please stop timer before completing task.',
                'type': 'warning',
                confirmButtonColor: '#6777EF',
            });
            currentCheckbox.prop('checked', false);
            return false;
        } else {
            if ($('#filter_status').val() != '') {
                $(this).parentsUntil('.task-list').toggle('slide');
            }
        }
        updateTaskStatus(taskId);
    });

    updateTaskStatus = (id) => {
        $.ajax({
            url: route('tasks.update-status',id),
            type: 'PUT',
            cache: false,
            data:{status:1},
            success: function (result) {
                // if (result.success) {
                    window.livewire.emit('refresh');
                    revokerTracker();
                // }
            },
        });
    };
});
$(document).ready(function () {
    $('#editEstimateTimeHours').datetimepicker({
        format: 'HH',
        useCurrent: false,
        locale: languageName == 'ar' ? 'en' : languageName,
        icons: {
            previous: 'icon-arrow-left icons',
            next: 'icon-arrow-right icons',
            up: 'icon-arrow-up icons',
            down: 'icon-arrow-down icons',
        },
        sideBySide: true,
    });

    $('#editEstimateTimeDays').hide();

    $(document).on('click', '#editHours', function () {
        $('#editDays').removeClass('btn btn-primary text-white');
        $(this).addClass('btn btn-primary text-white');
        $('#editTypes').val(0);
        $('#editEstimateTimeDays').hide().val('');
        $('#editEstimateTimeHours').show();
    });

    $(document).on('click', '#editDays', function () {
        $('#editHours').removeClass('btn btn-primary text-white');
        $(this).addClass('btn btn-primary text-white');
        $('#editTypes').val(1);
        $('#editEstimateTimeHours').hide().val('');
        $('#editEstimateTimeDays').show();
    });

    $(document).on('click', '#addTask', function (event) {
        event.preventDefault();
        let projectId = $(this).attr('data-id');
        $('#taskProjectId').val(projectId);
        $('#addTaskModal').appendTo('body').modal('show');
    });
});
