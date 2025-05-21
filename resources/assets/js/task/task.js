"use strict";
if (!isShow) {
    $(function () {
        $('#no-record-info-msg').hide();
        $('#user-drop-down-body').hide();

        $('#project_filter,#filter_user,#filter_task').
            select2({
                width: '100%',
                sorter: function(data) {
                    return data.sort(function (a, b) {
                        if (a.text > b.text) {
                            return 1;
                        }
                        if (a.text < b.text) {
                            return -1;
                        }
                        return 0;
                    });
                }
            });

        $('#filter_per_page').select2({
                width: '100%',
            });

        setTimeout(function () {
            $('#filter_user').select2({
                width: '100%',
            }).trigger('change');
        }, 100);

        $('#filter_status').select2({
            width: '100%',
        });
        $('#assignTo,#editAssignTo').select2({
            width: '100%',
            placeholder: 'Select Assignee',
        });
        $('#projectId,#editProjectId').select2({
            width: '100%',
            placeholder: 'Select Project',
        });
        $('#priority,#editPriority').select2({
            width: '100%',
        });
        $('#assignee,#editAssignee,#editAssigneeField').select2({
            width: '100%',
            placeholder: 'Select Assignee',
        });
        if (canManageTags) {
            $('#tagIds,#editTagIds').select2({
                width: '100%',
                tags: true,
                placeholder: 'Select Tags',
                createTag: function (tag) {
                    let found = false;
                    $('#tagIds option').each(function () {
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
            $('#tagIds,#editTagIds').select2({
                width: '100%',
                placeholder: 'Select Tags',
            });
        }

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

        $('#dueDateFilter, #dueDate').datetimepicker({
            format: 'YYYY-MM-DD',
            useCurrent: false,
            locale: languageName == 'ar' ? 'en' : languageName,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom',
            },
            icons: {
                previous: 'icon-arrow-left icons',
                next: 'icon-arrow-right icons',
                clear: 'icon-trash icons',
            },
            sideBySide: true,
            showClear: true,
        });

        $('#dueDateFilter, #dueDate, #editDueDate').on('dp.show', function () {
            matchWindowScreenPixels({
                dueDateFilter: '#dueDateFilter',
                dueDate: '#dueDate',
                editDueDate: '#editDueDate',
            }, 'tsk');
        });
        tbl.ajax.reload();

        $('input.complete-task-checkbox').prop('disabled', true);
        $(document).ajaxComplete(function (result) {
            $('input.complete-task-checkbox').prop('disabled', false);
        });

        $(window).resize(function () {
            matchWindowScreenPixels({
                dueDateFilter: '#dueDateFilter',
                dueDate: '#dueDate',
                editDueDate: '#editDueDate',
            }, 'tsk');
        }).trigger('resize');

        tbl.ajax.reload();

        $('.editDueDate').on('dp.show', function () {
            const windowWidth = $(window).innerWidth();
            if (windowWidth === 320) {
                $('.due-date-wrapper .editDueDate + .bootstrap-datetimepicker-widget.dropdown-menu').
                    addClass('dtPickerForListing320-tsk');
            }
        });

        $('input.complete-task-checkbox').prop('disabled', true);
        $(document).ajaxComplete(function (result) {
            $('input.complete-task-checkbox').prop('disabled', false);
        });

        $('[data-toggle="tooltip"]').tooltip();
    })
}

$(document).ready(function () {
    scrollToTheBottom();
    $('.nav-tabs a').on('show.bs.tab', function () {
        if ($(this).data('for-comment') === 1)
            scrollToTheBottom();
    });
});

window.scrollToTheBottom = function () {
    setTimeout(function () {
        let height = $('#itemsWrapper').outerHeight();
        $('.task-chat-content').scrollTop(height * height);
    }, 200);
};

let taskAssignees = [];
let editTaskAssignees = [];

function getRandomColor () {
    let num = Math.floor(Math.random() * 12) + 1;
    let coloCodes = [
        '0095ff',
        '9594fe',
        'da4342',
        '8e751c',
        'ac1f87',
        'c86069',
        '370e1c',
        'ca4e7d',
        'c02bd8',
        '289e05',
        '3aad14',
        '0D8ABC',
        '511852']
    return coloCodes[num]
}

if (!isShow) {
    var tbl = $('#task_table').DataTable({
        processing: true,
        serverSide: true,
        'order': [[5, 'desc']],
        ajax: {
            url: route('tasks.index'),
            data: function (data) {
                data.filter_project = $('#project_filter').
                    find('option:selected').
                    val();
                data.filter_user = $('#filter_user').
                    find('option:selected').
                    val();
                data.filter_status = $('#filter_status').
                    find('option:selected').
                    val();
                data.due_date_filter = $('#dueDateFilter').val();
            },
        },
        columnDefs: [
            {
                'targets': [7],
                'orderable': false,
            'width': '9%',
        },
        {
            'targets': [0],
            'width': '2%',
            'className': 'text-center',
            'orderable': false,
        },
        {
            'targets': [2],
            'orderable': false,
        },
        {
            'targets': [3],
            'width': '6%',
        },
        {
            'targets': [4, 5],
            'width': '10%',
            'className': 'text-center',
        },
        {
            'targets': [6],
            'width': '6%',
            'className': 'text-center',
        },
    ],
    columns: [
        {
            data: function (row) {
                return row.status == 1
                    ? '<div class="active_btn" title="Mark as pending"><input name="yes" id="enabled" class="enabled" type="checkbox" checked data-check="' +
                    row.id + '"></div>'
                    : '<div class="active_btn" title="Mark as complete"><input name="no" id="disabled" type="checkbox" class="enabled" data-check="' +
                    row.id + '"></div>'
            }, name: 'status',
        },
        {
            data: function (row) {
                let url = taskUrl + row.project.prefix + '-' + row.task_number
                return '<a href="' + url + '">' + row.title + '</a>'
            },
            name: 'title',
        },
        {
            data: function (row) {
                let imgStr = '';
                $(row.task_assignee).each(function (i, e) {
                    imgStr += '<img class="assignee__avatar" src="' +
                        e.img_avatar + '" data-toggle="tooltip" title="' +
                        e.name + '">';
                });

                return imgStr;
            }, name: 'taskAssignee.name',
        },
        {
            data: function (row) {
                const priority = row.priority;
                const styleText = 'style';
                const priorityColors = {
                    'highest': '#FF0000',
                    'high': '#FF3333',
                    'medium': '#FF8000',
                    'low': '#336600',
                    'lowest': '#4C9900',
                };

                return '<i class="fa fa-arrow-up" '+styleText+'="color: ' +
                    priorityColors[priority] + '"></i> ' +
                    priority.charAt(0).toUpperCase() + priority.slice(1);
            }, name: 'priority',
        },
        {
            data: function (row) {
                return row;
            },
            render: function (row) {
                if (row.due_date == null || row.due_date === '') {
                    return '';
                }

                let todayDate = (new Date()).toISOString().split('T')[0];
                if (row.status === 0 && todayDate > row.due_date) {
                    return '<span class="text-danger">' + format(row.due_date) + '</span>';
                }

                return format(row.due_date);
            },
            name: 'due_date',
        },
        {
            data: function (row) {
                return row
            },
            render: function (row) {
                return '<span data-toggle="tooltip" title="' +
                    format(row.created_at, 'hh:mm:ss a') + '">' +
                    format(row.created_at) + '</span>'
            },
            name: 'created_at',
        },
        {
            data: function (row) {
                if (row.created_user) {
                    return '<img class="assignee__avatar" src="' +
                        row.created_user.img_avatar +
                        '" data-toggle="tooltip" title="' +
                        row.created_user.name + '">'
                } else {
                    return ''
                }
            }, name: 'createdUser.name',
        },
        {
            data: function (row) {
                let taskAssignee = []
                $.each(row.task_assignee, function (key, value) {
                    taskAssignee.push(value.id)
                })
                let actionString =
                    '<a title="Details" data-toggle="modal" class="btn action-btn btn-info btn-sm taskDetails mr-1"  data-target="#taskDetailsModal" data-id="' +
                    row.id + '"> ' +
                    '<i class="fa fa-clock action-icon"></i></a>' +
                    '<a title="Edit" class="btn action-btn btn-primary btn-sm mr-1 task-edit-btn" data-id="' +
                    row.id + '">' +
                    '<i class="cui-pencil action-icon"></i>' + '</a>' +
                    '<a title="Delete" class="btn action-btn btn-danger btn-sm btn-task-delete" data-task-id="' +
                    row.id + '">' +
                    '<i class="cui-trash action-icon"></i></a>'

                if ($.inArray(loggedInUserId, taskAssignee) > -1) {
                    actionString += '<a title="Add Time Entry" class="btn btn-success action-btn btn-sm entry-model ml-1" data-toggle="modal" data-target="#timeEntryAddModal" data-id="' +
                        row.id + '" data-project-id="' + row.project.id + '">' +
                        '<i class="fa fa-user-clock action-icon"></i></a>'
                }

                return actionString
            }, name: 'id',
        },
    ],
    'fnInitComplete': function () {
        $('#project_filter,#filter_status,#filter_user').change(function () {
            tbl.ajax.reload();
        });

        $('#dueDateFilter').on('dp.change', function (e) {
            tbl.ajax.reload()
        })
    },
})
}
$('#task_table').on('draw.dt', function () {
    $('.tooltip').tooltip('hide')
    setTimeout(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
})

// open edit user model
$(document).on('click', '.task-edit-btn', function (event) {
    let id = $(event.currentTarget).attr('data-id');
    $.ajax({
        url: route('tasks.edit',id),
        type: 'GET',
        success: function (result) {
            if (result.success) {
                let task = result.data.task
                let allTags = result.data.tags
                let element = document.createElement('textarea')
                element.innerHTML = task.title
                $('#editTagIds').empty()
                $.each(allTags, function (i, e) {
                    $('#editTagIds').
                        append($('<option>', { value: i, text: e }))
                })

                let desc = task.description;
                quillEdit.clipboard.dangerouslyPasteHTML(0, desc);  // to set the HTML content to Quill Editor instance/container

                $('#tagId').val(task.id);
                $('#editTitle').val(element.value);
                $('#taskEditDescription').val(task.description);
                $('#editDueDate').val(task.due_date);
                $('#editProjectId').val(task.project.id).trigger('change');
                $('#editStatus').val(task.status);

                var tagsIds = [];
                var userIds = [];
                taskAssignees = []
                $(task.tags).each(function (i, e) {
                    tagsIds.push(e.id)
                })
                $(task.task_assignee).each(function (i, e) {
                    userIds.push(e.id)
                    taskAssignees.push(e.id)
                })
                $('#editTagIds').val(tagsIds).trigger('change')

                $('#editAssignee').val(userIds).trigger('change')
                $('#editPriority').val(task.priority).trigger('change')

                setTimeout(function () {
                    $.each(task.task_assignee, function (i, e) {
                        $('#editAssignee option[value=\'' + e.id + '\']').
                            prop('selected', true).
                            trigger('change')
                    })
                    $('#EditModal').appendTo('body').modal('show');

                }, 1500)
            }
        },
        error: function (error) {
            manageAjaxErrors(error)
        },
    })
})

// open delete confirmation model
$(document).on('click', '.task-delete-btn', function (event) {
    let id = $(event.currentTarget).attr('data-id');
    deleteItem(route('tasks.destroy',id), '#task_table', 'Task');
});

// open delete confirmation model
$(document).
    on('click', '.delete-recent-task,.delete-older-task', function (event) {
        let searchVal = $('#search').val();
        let id = $(event.currentTarget).attr('data-id');
        let runningTaskId = localStorage.getItem('currentTaskId');
        if (id == runningTaskId) {
            displayErrorMessage('Please stop task timer.');
            return false;
        }
        swal({
                title: deleteHeading + ' !',
                text: deleteMessage + ' "Task" ?',
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
                    url: route('tasks.destroy',id),
                    type: 'DELETE',
                    dataType: 'json',
                    success: function (obj) {
                        if (obj.success) {
                            window.livewire.emit('refresh');
                            $('#search').val(searchVal);
                        }
                        swal({
                            title: 'Deleted!',
                            text: 'Task has been deleted.',
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

$(document).on('submit', '#addNewForm', function (event) {
    event.preventDefault();
    let $description = $('<div />').html($('#taskDescription').summernote('code'));
    let empty = $description.text().trim().replace(/ \r\n\t/g, '') === '';
    let loadingButton = jQuery(this).find('#btnTaskSave');
    loadingButton.button('loading');

    if($('#taskDescription').summernote('isEmpty')){
        $('#taskDescription').val('');
    } else if (empty){
        displayErrorMessage('Description field is not contain only white space');
        loadingButton.button('reset');
        return false;
    }
    let formData = new FormData($(this)[0]);
    
    let files = $('#Add_attachment')[0].files
    for (let i = 0; i < files.length; i++) {
        formData.append('file[]', files[i]);
    }
    $.ajax({
        url: route('tasks.store'),
        type: 'POST',
        data:  formData,
        contentType: false,
        processData: false,
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#AddModal').modal('hide');
                $('#task_table').DataTable().ajax.reload();
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

$(document).on('submit', '.editForm', function (event) {
    let $description = $('<div />').html($('#taskEditDescriptionContainer').summernote('code'));
    let empty = $description.text().trim().replace(/ \r\n\t/g, '') === '';
    let form = $(this);
    event.preventDefault()
    let loadingButton = jQuery(this).find('[type="submit"]')
    loadingButton.button('loading');
    let id = $(this).find('.taskId').val();
    let stopwatchTaskId = getItemFromLocalStorage('task_id')
    let isClockRunning = getItemFromLocalStorage('clockRunning')
    if (id === stopwatchTaskId && isClockRunning === 'true') {
        tbl.ajax.reload();
        swal({
            'title': 'Warning',
            'text': 'Please stop timer before updating task.',
            'type': 'warning',
            confirmButtonColor: '#6777ef',
        });
        loadingButton.button('reset');
        return false;
    }
    if ($('#taskEditDescriptionContainer').summernote('isEmpty')) {
        $('#taskEditDescriptionContainer').val('');
    }else if (empty){
        displayErrorMessage('Description field is not contain only white space');
        loadingButton.button('reset');
        return false;
    }
    let formdata = $(this).serializeArray();

    $.ajax({
        url: route('tasks.update',id),
        type: 'put',
        data: formdata,
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#EditModal').modal('hide');
                $('#task_table').DataTable().ajax.reload();
                revokerTracker();
                window.livewire.emit('refresh');
            }
        },
        error: function (error) {
            manageAjaxErrors(error)
        },
        complete: function () {
            loadingButton.button('reset')
        },
    })
})

$('#AddModal').on('hidden.bs.modal', function () {
    $('#taskDescription').summernote('code', '');
    $('#projectId').val(null).trigger('change');
    $('#assignee').val(null).trigger('change');
    $('#tagIds').val(null).trigger('change');
    $('#priority').val(null).trigger('change');
    $('#dueDate').data('DateTimePicker').clear();
    $('#estimateTimeHours').data('DateTimePicker').date('00:00');
    $('#Days').removeClass('btn btn-primary text-white');
    $('#Hours').addClass('btn btn-primary text-white');
    $('#types').val(0);
    $('#estimateTimeDays').hide().val('');
    $('#estimateTimeHours').show().val('')
    $('#previewImage').empty()
    document.getElementById('Add_attachment').files = null
    resetModalForm('#addNewForm', '#validationErrorsBox')
    init()
})

$('#EditModal').on('hidden.bs.modal', function () {
    $('#taskEditDescription').summernote('code', '')
    $('#attachments').empty()
    $('#previewImage').empty()
    $('#no_attachments').empty()
    $('#previewImageEdit').empty()
    resetModalForm('#editForm', '#editValidationErrorsBox')
})
let updateTaskStatus;
let currentCheckbox;
$(function () {

    $(document).on('change', 'input.complete-task-checkbox', function (e) {
        let taskId = ($(this).attr('data-check'));
        currentCheckbox = $(this);
        let stopwatchTaskId = getItemFromLocalStorage('task_id');
        let isClockRunning = getItemFromLocalStorage('clockRunning');
        if (taskId === stopwatchTaskId && isClockRunning === 'true') {
            tbl.ajax.reload();
            swal({
                'title': 'Warning',
                'text': 'Please stop timer before completing task.',
                'type': 'warning',
                confirmButtonColor: '#6777EF',
            });
            currentCheckbox.prop('checked', false)
            return false
        } else {
            if ($('#filter_status').val() != '') {
                $(this).parentsUntil('.task-list').toggle('slide')
            }
        }
        if (typeof isTask != 'undefined') {
            updateTaskStatus(taskId)
        }
    });

    updateTaskStatus = (id) => {
        $.ajax({
            url: route('task.update-status',id),
            type: 'POST',
            cache: false,
            success: function (result) {
                if (result.success) {
                    window.livewire.emit('refresh');
                    revokerTracker();
                }
            },
        })
    }
})

window.manageCollapseIcon = function (id) {
    var isExpanded = $('#tdCollapse' + id).attr('aria-expanded')
    if (isExpanded === 'true') {
        $('#tdCollapse' + id).find('a span').removeClass('fa-minus-circle');
        $('#tdCollapse' + id).find('a span').addClass('fa-plus-circle');
    } else {
        $('#tdCollapse' + id).find('a span').removeClass('fa-plus-circle');
        $('#tdCollapse' + id).find('a span').addClass('fa-minus-circle');
    }
}

window.deleteTimeEntry = function (timeEntryId) {
    swal({
            title: 'Delete !',
            text: 'Are you sure you want to delete this Time Entry?',
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
                url: route('time-entries.destroy',timeEntryId),
                type: 'DELETE',
                dataType: 'json',
                success: function (obj) {
                    if (obj.success) {
                        $('.close').trigger('click')
                    }
                    swal({
                        title: 'Deleted!',
                        text: 'Time Entry has been deleted.',
                        type: 'success',
                        confirmButtonColor: '#6777EF',
                        timer: 2000,
                    })
                },
                error: function (data) {
                    swal({
                        title: '',
                        text: data.responseJSON.message,
                        type: 'error',
                        confirmButtonColor: '#6777EF',
                        timer: 5000,
                    })
                },
            })
        })
}

function setTaskDrp (id) {
    $('#taskId').val(id).trigger('change')
    $('#taskId').prop('disabled', true)
}

$(document).on('click', '.timeEntryAddModal', function (event) {
    $('#timeEntryAddModal').appendTo('body').modal('show');
    let taskId = $(event.currentTarget).attr('data-id');
    let projectId = $(event.currentTarget).attr('data-project-id');
    $('#timeProjectId').val(projectId).trigger('change');
    getTasksByProject(projectId, '#taskId', taskId, '#tmValidationErrorsBox');

    setTimeout(function () {
        $('#taskId').val(taskId).trigger('change');
    }, 1500);
});

$('#taskDescription,#taskEditDescription').summernote({
    placeholder: 'Add Task description...',
    minHeight: 200,
    toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough']],
        ['para', ['paragraph']]],
});

$(document).on('change', '#projectId', function (event) {
    let projectId = $(this).val();
    loadProjectAssignees(projectId, 'assignee');
});

$(document).on('change', '#editProjectId', function (event) {
    let projectId = $(this).val()
    loadProjectAssignees(projectId, 'editAssignee')
    setTimeout(function () {
        $('#editAssignee').val(projectTaskAssignees).trigger('change');
    }, 1500)
})

function loadProjectAssignees (projectId, selector) {
    editTaskAssignees = [];
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
                    editTaskAssignees.push(key);
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

$(document).on('focusout', '.task-input', function () {
    if ($(this).val().length > 250) {
        displayErrorMessage('The title may not be greater than 250 characters');
        return false;
    }
    $('.task-name').addClass('disabled');
    let taskId = $(this).attr('data-id');
    let taskName = currentInput.val();
    if (taskName === oldTaskName) {
        $('.task-name').removeClass('disabled');
        $(this).hide();
        $(this).next('.task-name').show();
        return false;
    }
    taskItemLock($(this));
    if (taskName.trim() == '') {
        $(this).addClass('error');
        displayErrorMessage('Enter task name');
        return false;
    }
    $(this).removeClass('error');
    window.livewire.emit('updateTask', taskName, taskId);
});

//modal not closed on click outside
    $('.modal').modal({ show: false, backdrop: 'static' })

$(document).on('click', '.addTasksModal', function () {
    $('#notFoundYet').text('No attachments added yet')
    $('#AddModal').appendTo('body').modal('show')
    $('#projectId').select2({
        width: '100%',
        placeholder: 'Select Project',
    })
});

$(document).on('keypress', '.task-input', function (e) {
    if(e.which == 13){
        $(this).blur();
    }
});

let currentInput;
let oldTaskName;
let taskIdForDate;
let descriptionEditorArr = [];
document.addEventListener("livewire:load", function(event) {
    window.livewire.hook('message.processed', () => {
        $('[rel=tooltip]').tooltip({ placement: 'top' })
        $(document).on('click', '.task-name', function () {
            oldTaskName = $(this).text()
            $(this).hide()
            currentInput = $(this).prev('input')
            currentInput.show().val(oldTaskName).focus()
        })

        let editDueDate = document.querySelectorAll('.editDueDate')
        if (typeof isTask != 'undefined') {
            $(editDueDate).datetimepicker({
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
        }
        $('.editPriority').select2({
            width: '100%',
        })

        if (canManageTags) {
            $('.editTagIds').select2({
                width: '100%',
                placeholder: 'Select Tags',
                tags: true,
                sorter: function (data) {
                    return data.sort(function (a, b) {
                        if (a.text > b.text) {
                            return 1;
                        }
                        if (a.text < b.text) {
                            return -1;
                        }
                        return 0;
                    });
                },
                createTag: function (tag) {
                    let element = document.createElement('textarea');
                    element.innerHTML = tag.term;
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
                            text: element.value,
                        };
                    }
                },
            });
        } else {
            $('.editTagIds').select2({
                width: '100%',
                placeholder: 'Select Tags',
            });
        }

        $('.editProjectIds').select2({
            width: '100%',
            placeholder: 'Select Project',
            sorter: function(data) {
                return data.sort(function (a, b) {
                    if (a.text > b.text) {
                        return 1;
                    }
                    if (a.text < b.text) {
                        return -1;
                    }
                    return 0;
                });
            },
        });

        let editors = document.querySelectorAll('div.task-detail');
        let count = 0;
        $.each(editors, function (i, v) {
            $('.taskEditDescriptionContainer-' + i).summernote('destroy');
        });
        $.each(editors, function (i, v) {
            descriptionEditorArr[count++] = $(
                '.taskEditDescriptionContainer-' + i).summernote(
                {
                    placeholder: 'Add Task description...',
                    minHeight: 200,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough']],
                        ['para', ['paragraph']]],
                });
        })

    });
});

$(document).on('dp.change', '.editDueDate', function (e) {
    let date = $(this).val();
    window.livewire.emit('updateTaskDueDate', date, taskIdForDate);
});

$(document).on('click', '.editDueDate', function () {
    taskIdForDate = $(this).attr('data-id');
});

$(document).on('click', '.task-detail-link', function () {
    $('.task-detail-card').toggleClass('toggle');
});

$(document).ready(function () {
    $('#filter_status').trigger('change');
})

$('#filter_status').change(function () {
    window.livewire.emit('filterTasksByStatus', $(this).val());
});

$('#project_filter').change(function () {
    window.livewire.emit('filterTasksByProject', $(this).val());
});

$(document).on('change', '#filter_user', function () {
    $('#project_filter').empty();
    let id = isEmpty($(this).val()) ? loginUserId : $(this).val();
    $.ajax({
        url: route('project-by-users',id),
        type: 'GET',
        success: function (result) {
            if (result.success) {
                const projects = result.data;
                $('#project_filter').
                    find('option').
                    remove().
                    end().
                    append('<option value="">All</option>');
                for (const key in projects) {
                    if (projects.hasOwnProperty(key)) {
                        $('#project_filter').
                            append($('<option>',
                                { value: key, text: projects[key] }));
                    }
                }
            }
        },
        error: function (result) {
            manageAjaxErrors(result);
        },
    });
    window.livewire.emit('filterTasksByUser', $(this).val());
});

$('#dueDateFilter').on('dp.change', function (e) {
    window.livewire.emit('filterTasksByDueDate', $(this).val());
});

$('#filter_per_page').change(function (e) {
    window.livewire.emit('filterPerPage', $(this).val());
});

$('#filter_task').change(function (e) {
    window.livewire.emit('orderByFilter', $(this).val());
});

$(document).on('click', '.edit-task-assignees', function (event) {
    let id = $(event.currentTarget).attr('data-id');
    startLoader();
    $.ajax({
        url: route('task.edit-assignee',id),
        type: 'GET',
        success: function (result) {
            if (result.success) {
                let task = result.data.task;
                const users = result.data.users;
                $('#editAssigneeField').empty();
                for (const key in users) {
                    if (users.hasOwnProperty(key)) {
                        $('#editAssigneeField').
                            append($('<option>', { value: key, text: users[key] }));
                    }
                }
                $('#hdnTaskId').val(task.id);

                let userIds = result.data.task_assignee;
                taskAssignees = result.data.task_assignee

                $('#editAssigneeField').val(userIds).trigger('change')
                setTimeout(function () {
                    $("#editAssigneeField").val(result.data.task_assignee).trigger('change');
                }, 1000)

                stopLoader();
                $('#EditAssigneeModal').appendTo('body').modal('show');
            }
        },
        error: function (error) {
            manageAjaxErrors(error)
        },
    })
});

$(document).on('click', '#EditAssigneeModal #btnSaveAssignees', function () {
    var loadingButton = jQuery(this);
    loadingButton.button('loading');
    window.livewire.emit('updateAssignees', $('#editAssigneeField').val(),
        $('#hdnTaskId').val());
    $('#EditAssigneeModal').modal('hide');
    $('#EditAssigneeModal').on('hidden.bs.modal', function () {
        loadingButton.button('reset');
    });
});

const taskItemLock = function (el) {
    el.closest('.task-item').addClass('loading');
    setTimeout(function () {
        el.closest('.task-item').removeClass('loading');
    }, 3000);
};

let recordIndex = null;
let editPriority = null;
let editTags = null;
let editProject = null;
let editDescription = null;
$(document).on('click', '.editRecord', function () {
    recordIndex = $(this).data('record-index');
    editPriority = $(document).
        find('[data-edit-priority=\'' + recordIndex + '\']').
        val();
    editTags = $(document).
        find('[data-edit-tags=\'' + recordIndex + '\']').
        val().
        toString().
        split(',');
    editProject = $(document).
        find('[data-edit-project=\'' + recordIndex + '\']').
        val();
    editDescription = $(descriptionEditorArr[recordIndex]).summernote('code');
});

$(document).on('click', '.elementCancel', function () {
    $(document).
        find('[data-edit-priority=\'' + recordIndex + '\']').
        val(editPriority).
        trigger('change');
    $(document).
        find('[data-edit-tags=\'' + recordIndex + '\']').
        val(editTags).
        trigger('change');
    $(document).
        find('[data-edit-project=\'' + recordIndex + '\']').
        val(editProject).
        trigger('change');
    $(descriptionEditorArr[recordIndex]).summernote('code', editDescription);
});

$('.task-action .dropdown').hover(function () {
    $(this).find('.dropdown-menu').stop(true, true).delay(100).fadeIn(500);
}, function () {
    $(this).find('.dropdown-menu').stop(true, true).delay(100).fadeOut(500);
});

$('#dropdownMenuButton2').click(function () {
    return false;
});

$('.dropdown-large').on('click', function (event) {
    if ($(this).parent().hasClass('show')) {
        $(this).parent().toggleClass('show');
    } else {
        $(this).parent().removeClass('show');
    }
});

$(document).on('click', '.close', function () {
    $('.dropdown-large').removeClass('show');
});

$(document).ready(function () {
    $('#estimateTimeHours').datetimepicker({
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
    });

    $('#estimateTimeDays').hide();

    $(document).on('click', '#Hours', function () {
        $('#Days').removeClass('btn btn-primary text-white');
        $(this).addClass('btn btn-primary text-white');
        $('#types').val(0);
        $('#estimateTimeDays').hide().val('');
        $('#estimateTimeHours').show();
    });

    $(document).on('click', '#Days', function () {
        $('#Hours').removeClass('btn btn-primary text-white');
        $(this).addClass('btn btn-primary text-white');
        $('#types').val(1);
        $('#estimateTimeHours').data('DateTimePicker').date('00:00');
        $('#estimateTimeHours').hide().val('');
        $('#estimateTimeDays').show();
    });
});

$(document).on('click', '#resetFilters', function () {
    $('#project_filter').val(null).trigger('change');
    $('#dueDateFilter').data('DateTimePicker').clear();
    $('#filter_status').val(0).trigger('change');
    $('#filter_per_page').val(10).trigger('change');
    $('#filter_task').val(orderId).trigger('change');
    if (canManageProjects) {
        $('#filter_user').val(loggedInUserId).trigger('change')
        window.livewire.emit('filterTasksByUser', $('#filter_user').val())
    }
    window.livewire.emit(['filterTasksByProject', $('#project_filter').val()],
        ['filterTasksByDueDate', $('#dueDateFilter').val()],
        ['filterTasksByStatus', $('#filter_status').val()],
        ['filterPerPage', $('#filter_per_page').val()],
        ['orderByFilter', $('#filter_task').val()])
});

document.addEventListener('DOMContentLoaded', init, false)

function init () {
    document.querySelector('#Add_attachment').
        addEventListener('change', handleFileSelect, false)
}

$('.btn-upload').hide()
let imageArray = []
const dt = new DataTransfer()
let files = {}

function handleFileSelect (e) {
    e.preventDefault()
    $('#notFoundYet').text('')
    files = e.target.files
    let html = ''
    for (let i = 0; i < files.length; i++) {
        let ext = files[i].name.split('.').pop().toLowerCase()
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
                'The attachment must be a file of type: jpeg, jpg, png, xls, xlsx, pdf, doc')
            $('#notFoundYet').
                append('No attachments added yet').
                addClass('text-center')
            $('#previewImage').empty()
            return false
        } else {
            imageArray.push(i)
            if (files[i].size > 1000000) {
                displayErrorMessage(
                    'The attachment size should not greater than 12 mb.')
                $(this).val('')
                if (imageArray.length == 1) {
                    $('#notFoundYet').
                        append('No attachments added yet').
                        addClass('text-center')
                }

                return false
            }
            var tmppath = URL.createObjectURL(e.target.files[i])
            if (ext === 'xlsx' || ext === 'xls' || ext === 'csv') {
                tmppath = '/assets/img/xls_icon.png'
            }
            if (ext === 'pdf') {
                tmppath = '/assets/img/pdf_icon.png'
            }
            if (ext === 'docx' || ext === 'doc') {
                tmppath = '/assets/img/doc_icon.png'
            }
            html += `
                <div class="col-sm-3 mt-3 text-center" id="divImageId${i}">
           
                                    <a class="fancybox preview-image-thumb" target="_blank"
                                                                   alt="#"> <img class="x-image"  src="${tmppath}" />
                                                               </a>
                                                     
                 <div class="x-details">
                      
                                                                <div class="x-actions"><strong>
                                                                        <a href="" class="download-attachment"><?php echo __('messages.expense.download')?> <span class="x-icons"><i class="ti-download"></i></span></a></strong>
                                                                    <span>
                                                             
                                                                    <a href="javascript:void(0)"
                                                                                class="text-danger delete-attachment" data-id=""><?php echo __('messages.common.delete') ?> </a> </span>
                                                                                <a href="javascript:void(0)" class="text-danger delete-add-attachment text-center text-decoration-none "   data-key="${i}" data-id="divImageId${i}">Delete</a>
                                                               
                                                                </div>
                                                            </div>
                 </div>`
        }
    }
    $('.previewImage').html(html)

    // for (let file of this.files) {
    //     dt.items.add(file);
    // }
    // this.files = dt.files;
}

$(document).on('click', '.delete-add-attachment', function (e) {
    let divId = $(e.currentTarget).attr('data-id')
    let dataKey = $(e.currentTarget).attr('data-key')
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
            let indexItem = imageArray.indexOf(dataKey)
            imageArray.splice(indexItem, 1)
            swal({
                title: 'Deleted!',
                text: 'Attachment has been deleted.',
                type: 'success',
                confirmButtonColor: '#6777EF',
                timer: 2000,
            })

            dt.items.remove(dataKey)
            document.getElementById('Add_attachment').files = dt.files

            $('#' + divId + '').remove()
            if (imageArray.length == 0) {
                $('#notFoundYet').
                    append('No attachments added yet').
                    addClass('text-center')
            }
        })
})

document.addEventListener('DOMContentLoaded', addInit, false)

function addInit () {
    if (isShowProject != true) {
        document.querySelector('#editTaskAddAttachment').
            addEventListener('change', handleFileSelectTask, false)
    }
}

$('.btn-upload').hide()

function handleFileSelectTask (e) {
    e.preventDefault()
    let files = e.target.files
    let taskId = $('#tagId').val()
    if (files.length != 0) {
        // $('.choose-button').hide();
        // $('.btn-upload').show();
    }
    for (let i = 0; i < files.length; i++) {
        let ext = files[i].name.split('.').pop().toLowerCase()
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
                'The attachment must be a file of type: jpeg, jpg, png, xls, xlsx, pdf, doc')
            $(this).val('')
            // $('.choose-button').show();
            // $('.btn-upload').hide();
            return false
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
                    let attachments = result.data
                    if (result.success) {
                        // $('.btn-upload').hide();
                        // $('.choose-button').show();
                        $('#noAttachmentFound').empty()
                        // for (let i = 0; attachments.total_files > i; i++) {
                        let ext = attachments.file_name.split('.').
                            pop()
                        let image = []
                        let attachments_data = []
                        if (ext === 'png' || 'jpg' || 'jpeg' || 'PNG' ||
                            'JPG') {
                            image = attachments.file_url
                        }
                        if (ext === 'xlsx' || ext === 'xls' || ext === 'csv') {

                            image = '/assets/img/xls_icon.png'
                        }
                        if (ext === 'pdf') {
                            image = '/assets/img/pdf_icon.png'
                        }
                        if (ext === 'docx' || ext === 'doc') {
                            image = '/assets/img/doc_icon.png'
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
                                'createdId': attachments.userId,
                                'loginUserId': loggedInUserId,
                            }]
                        $('#previewImageEdit').
                            append(prepareTemplateRender('#attachmentImage',
                                attachments_data))
                    }
                    // }
                    scrollToTheBottomAttachment()
                },
                error: function (result) {
                    manageAjaxErrors(result)
                },
                complete: function () {
                    // loadingButton.button('reset');
                    // $('.btn-upload').removeClass('disabled');
                },
            })
        }
    }
}

window.scrollToTheBottomAttachment = function () {
    setTimeout(function () {
        let height = $('#card-attachments-container-edit').outerHeight()
        $('.attachments-content-edit').scrollTop(height * height)
    }, 200)
}

$(document).on('click', '.delete-attachment', function () {
    let attachmentId = $(this).attr('data-id')
    let taskId = $('#taskId').val()
    let divId = $(this).parent().parent().parent().parent().prop('id')
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
                url: route('task.delete-attachment', attachmentId),
                data: { filename: name, id: taskId },
                success: function () {
                    swal({
                        title: 'Deleted!',
                        text: 'Attachment has been deleted.',
                        type: 'success',
                        confirmButtonColor: '#6777EF',
                        timer: 2000,
                    })

                    $('#' + divId + '').remove()
                    if (!$.trim($('#previewImageEdit').html())) {
                        $('#noAttachmentFound').
                            append('No attachments added yet').
                            addClass('text-center')
                    }
                },
                error: function (e) {
                    manageAjaxErrors()
                },
            })
        })
})
