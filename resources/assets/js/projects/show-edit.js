'use strict'

let editPickr = ''
if ($('.color-wrapper').length != 0) {
    editPickr = Pickr.create({
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
            },
        },
    });
    editPickr.on('change', function () {
        const color = editPickr.getColor().toHEXA().toString()
        if (wc_hex_is_light(color)) {
            $('.editValidationErrorsBox').text('')
            $('.editValidationErrorsBox').show().html('')
            $('.editValidationErrorsBox').text('Pick a different color')
            setTimeout(function () {
                $('.editValidationErrorsBox').slideUp()
            }, 5000)
            $(':input[id="btnEditSave"]').prop('disabled', true)
            return
        }
        $(':input[id="btnEditSave"]').prop('disabled', false)
        editPickr.setColor(color)
        $('#edit_color').val(color)
    })
}

$(document).ready(function () {
    $('#client_id, #edit_client_id').select2({
        width: canManageClients ? 'calc(100% - 44px)' : '100%',
        placeholder: 'Select Client',
    })
    $('#department_id').select2({
        width: '100%',
        placeholder: 'Select Department',
    })
    $('#editCurrency,#edit_budget_type,#budget_type,#editStatusProject').
        select2({
            width: '100%',
        });

    $('#edit_user_ids').select2({
        width: '100%',
        placeholder: 'Select Users',
    });
});

$('#editDescription').summernote({
    placeholder: 'Add Project description...',
    minHeight: 200,
    toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough']],
        ['para', ['paragraph']]],
});

$('#ProjectEditModal').on('show.bs.modal', function () {
    $('.pcr-button').css('border', '1px solid grey');
    $('.project_remaining_user').popover('hide');
});

$(document).on('submit', '#editFormProject', function (event) {
    event.preventDefault();
    let form = $(this);
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
    var loadingButton = jQuery(this).find('#btnEditSave');
    loadingButton.button('loading');
    if ($('#editDescription').summernote('isEmpty')) {
        $('#editDescription').val('');
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
                location.reload(true);
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            loadingButton.button('reset')
        },
    });
});

$('#ProjectEditModal').on('hidden.bs.modal', function () {
    $('#editDescription').summernote('code', '')
    resetModalForm('#editFormProject', '#editValidationErrorsBox')
})

window.renderData = function (id) {
    $.ajax({
        url: route('projects.edit', id),
        type: 'GET',
        success: function (result) {
            if (result.success) {
                let project = result.data.project
                let element = document.createElement('textarea')
                element.innerHTML = project.name
                $('#projectId').val(project.id)
                $('#edit_name').val(element.value)
                $('#edit_prefix').val(project.prefix)
                editPickr.setColor(project.color)
                $('#edit_client_id').val(project.client_id).trigger('change')
                $('#editStatusProject').val(project.status).trigger('change')
                $('#editDescription').summernote('code', project.description)
                if (!isEmpty(project.price)) {
                    $('#editPrice').val(getFormattedPrice(project.price))
                }
                $('#editCurrency').val(project.currency).trigger('change')
                $('#edit_budget_type').
                    val(project.budget_type).
                    trigger('change')
                var valArr = result.data.users
                $('#edit_user_ids').val(valArr)
                $('#edit_user_ids').trigger('change')
                $('#ProjectEditModal').modal('show')
            }
        },
        error: function (result) {
            manageAjaxErrors(result)
        },
    })
}

$(document).on('click', '.edit-btn', function (event) {
    let projectId = $(event.currentTarget).attr('data-id')
    renderData(projectId)
})

$('.modal').on('show.bs.modal', function () {
    $(this).appendTo('body')
})

//popover on more users  of  project detail screen
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
        width: 'calc(100% - 44px)',
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
                $('#tagId').val(task.id)
                $('#ThisTaskId').val(task.id)
                $('#editTitle').val(element.value)
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
                    $('#editDays').removeClass('btn-primary text-white');
                    $('#editTypes').val(0);
                } else {
                    $('#editEstimateTimeHours').hide()
                    $('#editEstimateTimeDays').val(task.estimate_time).show()
                    $('#editDays').addClass('btn-primary text-white')
                    $('#editHours').removeClass('btn-primary text-white')
                    $('#editTypes').val(1)
                }
                let tagsIds = []
                let userIds = []
                $(task.tags).each(function (i, e) {
                    tagsIds.push(e.id)
                })
                $('#previewImageEdit').empty()
                $(task.media).each(function (i, e) {

                    let ext = task.attachments[i].file_name.split('.').
                        pop().
                        toLowerCase()

                    let image = []
                    if (ext === 'png' || 'jpg' || 'jpeg' || 'PNG' || 'JPG') {
                        image = task.attachments[i].original_url
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
                    let attachments_data = [];
                    attachments_data = [{
                        'id': task.attachments[i].id,
                        'url': task.attachments[i].original_url,
                        'image': image,
                        'username': task.created_user.name,
                        'downloadTask': route('tasks.index'),
                        'updated_at': moment(task.attachments[i].updated_at).
                            fromNow(),
                        'createdId': task.created_user.id,
                        'loginUserId': loggedInUserId,
                    }];
                    $('#previewImageEdit').
                        append(prepareTemplateRender('#attachmentImage',
                            attachments_data))
                })
                if (task.media.length == 0) {
                    $('#noAttachmentFound').text('No attachments added yet')
                }
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
    quillTask.summernote('code', '')
    $('#editDueDate').data('DateTimePicker').clear()
    $('#editEstimateTimeHours').data('DateTimePicker').date('00:00')
    $('#editDays').removeClass('btn btn-primary text-white')
    $('#editHours').addClass('btn btn-primary text-white')
    $('#editTypes').val(0)
    $('#editEstimateTimeDays').hide().val('')
    $('#editEstimateTimeHours').show().val('')
    $('#previewImageEdit').empty()
    $('#noAttachmentFound').empty()
    $(this).removeClass('disabled')
    resetModalForm('#editForm', '#editValidationErrorsBox')
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
            url: route('tasks.update-status', id),
            type: 'PUT',
            cache: false,
            data: { status: 1 },
            success: function (result) {
                // if (result.success) {
                window.livewire.emit('refresh')
                revokerTracker()
                // }
            },
        });
    };
});

$(document).ready(function () {
    $('#editEstimateTimeHours').datetimepicker({
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
        $('#editEstimateTimeHours').data('DateTimePicker').date('00:00');
        $('#editEstimateTimeHours').hide().val('');
        $('#editEstimateTimeDays').show();
    });

    $(document).on('submit', '#addNewStatusForm', function (event) {
        event.preventDefault();
        var loadingButton = jQuery(this).find('#btnSave');
        loadingButton.button('loading');
        $.ajax({
            url: '/status',
            type: 'POST',
            data: $(this).serialize(),
            success: function (result) {
                $("#editStatus").empty();
                if (result.success) {
                    displaySuccessMessage(result.message);
                    var option = "<option value=''>Select Status</option>";
                    $.each(result.data.statuses, function(key, value) {
                        option += "<option value='"+value+"'>"+key+"</option>";
                        $("#editStatus").html(option);
                    });
                    $('#editStatus').val(result.data.status.status).trigger('change');
                    $('#addStatusModal').modal('hide');
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

    $(document).on('click', '.add_modal', function (event) {
        event.preventDefault();
        $('#addStatusModal').appendTo('body').modal('show');
    });

    $('#addStatusModal').on('hidden.bs.modal', function () {
        resetModalForm('#addNewStatusForm', '#validationErrorsBox');
    });

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
                        $("#edit_client_id").html(option);
                    });
                    if ($('#ProjectEditModal').hasClass('show')) {
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

    $(document).on('click', '#addTask', function (event) {
        event.preventDefault()
        let projectId = $(this).attr('data-id')
        $('#taskProjectId').val(projectId)
        $('#notFoundYet').text('No attachments added yet')
        $('#addTaskModal').appendTo('body').modal('show')
    })

});

document.addEventListener('DOMContentLoaded', init, false)

function init () {
    document.querySelector('#editTaskAddAttachment').
        addEventListener('change', handleFileSelect, false)
}

$('.btn-upload').hide()

function handleFileSelect (e) {
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
            let formData = new FormData()
            formData.append('file', files[i])
            if (files[i].size > 1000000) {
                displayErrorMessage(
                    'The attachment size should not greater than 12 mb.')
                $(this).val('')
                return false
            }

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

function getRandomString () {
    return Math.random().toString(36).substring(2, 8) +
        Math.random().toString(36).substring(2, 8)
}

//file upload dropzon js
Dropzone.options.dropzone = {
    maxFilesize: 12,
    maxFiles: 25,
    renameFile: function (file) {
        let dt = new Date()
        let time = dt.getTime()
        let randomString = getRandomString()
        return time + '_' + randomString + '_' + (file.name).replace(/\s/g, '').
            replace(/\(/g, '_').
            replace(/\)/g, '')
    },
    thumbnailWidth: 125,
    acceptedFiles: '.png,.jpeg,.jpg,.pdf,.doc,.docx,.xls,.xlsx,.csv,.zip,.html,.rar,.css,.js,.txt,.json',
    addRemoveLinks: true,
    dictFileTooBig: 'File is too big ({{filesize}}MB). Max filesize: {{maxFilesize}}MB.',
    dictRemoveFile: 'x',
    timeout: 50000,
    init: function () {
        let thisDropzone = this
        $.get(route('projects.attachments', projectId), function (data) {
            $.each(data.data, function (key, value) {
                let mockFile = { name: value.name, id: value.id }

                thisDropzone.options.addedfile.call(thisDropzone, mockFile,
                    mockFile.id)
                thisDropzone.options.thumbnail.call(thisDropzone, mockFile,
                    value.url)
                thisDropzone.emit('complete', mockFile)
                thisDropzone.emit('thumbnail', mockFile, value.url,
                    mockFile.id)
                $('.dz-remove').eq(key).attr('data-file-id', value.id)
                $('.dz-remove').eq(key).attr('data-file-url', value.url)
            })
        })
        this.on('thumbnail', function (file, dataUrl, mediaId = null) {
            $(file.previewTemplate).
                find('.dz-details').
                css('display', 'none')
            previewFile(file, dataUrl, mediaId)
            let fileNameExtArr = file.name.split('.')
            let fileName = fileNameExtArr[0].replace(/\s/g, '').
                replace(/\(/g, '_').
                replace(/\)/g, '')
            let ext = file.name.split('.').pop()
            let previewEle = ''
            let clickDownload = true
            $(file.previewElement).
                find('.download-link').
                on('click', function () {
                    clickDownload = false
                })
            if ($.inArray(ext, ['jpg', 'JPG', 'jpeg', 'png', 'PNG']) > -1) {
                previewEle = '<a class="' + fileName +
                    '" data-fancybox="gallery" href="' + dataUrl +
                    '" data-toggle="lightbox" data-gallery="example-gallery"></a>'
                $('.previewEle').append(previewEle)
            }

            file.previewElement.addEventListener('click', function () {
                if (clickDownload) {
                    let fileName = file.previewElement.querySelector(
                        '[data-dz-name]').innerHTML
                    let fileExt = fileName.split('.').pop()
                    if ($.inArray(fileExt,
                            ['jpg', 'JPG', 'jpeg', 'png', 'PNG']) >
                        -1) {
                        let onlyFileName = fileName.split('.')[0]
                        $('.' + onlyFileName).trigger('click')
                    } else {
                        window.open(dataUrl, '_blank')
                    }
                }
                clickDownload = true
            })
        })
        this.on('addedfile', function (file, dataUrl, mediaId = null) {
            previewFile(file, dataUrl, mediaId)
        })

        function previewFile (file, dataUrl, mediaId) {
            let downloadPath = dataUrl
            let ext = file.name.split('.').pop()
            if (ext == 'pdf') {
                $(file.previewElement).
                    find('.dz-image img').
                    attr('src', '/assets/img/pdf_icon.png')
            } else if (ext.indexOf('doc') != -1 || ext.indexOf('docx') !=
                -1) {
                $(file.previewElement).
                    find('.dz-image img').
                    attr('src', '/assets/img/doc_icon.png')
            } else if (ext.indexOf('xls') != -1 || ext.indexOf('xlsx') != -1 ||
                ext.indexOf('csv') !=
                -1) {
                $(file.previewElement).
                    find('.dz-image img').
                    attr('src', '/assets/img/xls_icon.png')
            } else if (ext.indexOf('zip') != -1) {
                $(file.previewElement).
                    find('.dz-image img').
                    attr('src', '/assets/img/zip.png')
            } else if (ext.indexOf('rar') != -1) {
                $(file.previewElement).
                    find('.dz-image img').
                    attr('src', '/assets/img/rar.png')
            } else if (ext.indexOf('html') != -1) {
                $(file.previewElement).
                    find('.dz-image img').
                    attr('src', '/assets/img/html.png')
            } else if (ext.indexOf('css') != -1) {
                $(file.previewElement).
                    find('.dz-image img').
                    attr('src', '/assets/img/css.png')
            } else if (ext.indexOf('json') != -1) {
                $(file.previewElement).
                    find('.dz-image img').
                    attr('src', '/assets/img/json.png')
            } else if (ext.indexOf('js') != -1) {
                $(file.previewElement).
                    find('.dz-image img').
                    attr('src', '/assets/img/js.png')
            } else if (ext.indexOf('txt') != -1) {
                $(file.previewElement).
                    find('.dz-image img').
                    attr('src', '/assets/img/text.png')
            }
            if ($(file.previewElement).find('.download-link').attr('href') ===
                'undefined') {
                $(file.previewElement).find('.download-link').hide()
            }
            if ($(file.previewElement).find('.download-link').length < 1) {
                var anchorEl = document.createElement('a')
                anchorEl.setAttribute('href',
                    route('projects.index') + '/media/' + mediaId +
                    '?project_id=' + projectId)
                anchorEl.setAttribute('class', 'download-link')
                anchorEl.innerHTML = '<br>Download'
                file.previewElement.appendChild(anchorEl)
            }
            $('.dz-image').
                last().
                find('img').
                attr({ width: '100%', height: '100%' })
        }
    },
    processing: function () {
        $('.dz-remove').html('x')
        $('.dz-details').hide()
    },
    removedfile: function (file) {
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
                let attachmentId = file.previewElement.querySelector(
                    '[data-file-id]').
                    getAttribute('data-file-id')
                screenLock()
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').
                            attr('content'),
                    },
                    type: 'post',
                    url: route('project.delete-attachment', attachmentId),
                    data: { filename: name, project_id: projectId },
                    complete: function () {
                        location.reload(true)
                    },
                    error: function (e) {
                        console.log('error', e)
                        displayErrorMessage(e.responseJSON.message)
                    },
                })
                let fileRef
                return (fileRef = file.previewElement) != null
                    ?
                    fileRef.parentNode.removeChild(file.previewElement)
                    : void 0
            })

    },
    success: function (file, response) {
        let attachment = response.data
        let fileuploded = file.previewElement.querySelector('[data-dz-name]')
        let fileName = attachment.file_name
        let fileNameExtArr = fileName.split('.')
        let newFileName = fileNameExtArr[0]
        let newFileExt = fileNameExtArr[1]
        let prevFileName = (fileuploded.innerHTML.split('.')[0]).replace(/\s/g,
            '').replace(/\(/g, '_').
            replace(/\)/g, '')
        fileuploded.innerHTML = fileName

        $(file.previewTemplate).
            find('.dz-remove').
            attr('data-file-id', attachment.id)
        $(file.previewTemplate).
            find('.dz-remove').
            attr('data-file-url', attachment.file_url)
        $(file.previewElement).
            find('.download-link').
            attr('href', route('projects.index') + '/media/' + attachment.id +
                '?project_id=' + projectId)
        if ($.inArray(newFileExt, ['jpg', 'JPG', 'jpeg', 'png', 'PNG']) >
            -1) {
            $('.previewEle').
                find('.' + prevFileName).
                attr('href', attachment.file_url)
            $('.previewEle').
                find('.' + prevFileName).
                attr('class', newFileName)
        } else {
            file.previewElement.addEventListener('click', function () {
                window.open(attachment.file_url, '_blank')
            })
            $(file.previewElement).
                find('.download-link').
                addClass('sdfaskdfjaksdfjaskldfjlasdf')
        }
    },
    error: function (file, response) {
        let ext = file.name.split('.').pop().toLowerCase()
        if ($.inArray(ext, [
            'png',
            'jpg',
            'jpeg',
            'xls',
            'xlsx',
            'csv',
            'pdf',
            'doc',
            'docx',
            'zip',
            'html',
            'css',
            'rar',
            'txt',
            'js',
            'json']) == -1) {
            swal({
                title: 'Error!',
                text: 'The attachment must be a file of type: jpeg, jpg, png, xls, xlsx, pdf, doc, zip, rar, html, css, js, txt, json, csv, docx',
                type: 'error',
                confirmButtonColor: '#6777EF',
                timer: 5000,
            })
        } else {
            if (response.message) {
                swal('Error!', response.message, 'error')
            } else {
                swal('Error!', response, 'error')
            }
        }
        let fileRef
        return (fileRef = file.previewElement) != null ?
            fileRef.parentNode.removeChild(file.previewElement) : void 0

        return false
    },
}
