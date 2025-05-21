$(document).ready(function () {
    'use strict'
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab', $(e.target).attr('href'))
    })

    var activeTab = localStorage.getItem('activeTab')
    if (activeTab) {
        $('.nav-tabs a[href="' + activeTab + '"]').tab('show')
        localStorage.removeItem('activeTab')
    }

    if (typeof isTask == 'undefined') {
        $('#taskId,#editTaskId').select2({
            width: '100%',
            placeholder: 'Select Task',
        })
    }
    $('#duration').prop('disabled', true)

    $('#timeProjectId,#editTimeProjectId').select2({
        width: '100%',
        placeholder: 'Select Project',
    })

    $('#filterUser,#filter_project').select2()

    $('#filterActivity').select2({
        width: '150px',
    });

    $('#activityTypeId,#editActivityTypeId').select2({
        width: '100%',
        placeholder: 'Select Activity Type',
    });

    $('#timeUserId,#editTimeUserId').select2({
        width: '100%',
        placeholder: 'Select User',
    });

    let isEdit = false;
    let editTaskId, editProjectId = null;
    if (isShow) {
        let timeRange = $('#time_range');
        const today = languageName == 'ar' ? moment().lang('en') : moment();
        let start = today.clone().startOf('month');
        let end = today.clone().endOf('month');
        let userId = $('#filterUser').val();
        const lastMonth = moment().startOf('month').subtract(1, 'days');

        $(document).ready(function () {
            if (languageName == 'ar') {
                timeRange.val(start.lang('en').format('YYYY-MM-DD') + ' - ' +
                    end.lang('en').format('YYYY-MM-DD'));
            }else{
                timeRange.val(start.format('YYYY-MM-DD') + ' - ' +
                    end.format('YYYY-MM-DD'));
            }
                
            tbl.ajax.reload();
        });

        // Time Entries filter script
        window.cb = function (start, end) {
            if (start._isValid && end._isValid) {
                if (languageName == 'ar') {
                    timeRange.find('span').
                        html(start.lang('en').format('MMM D, YYYY') + ' - ' +
                            end.lang('en').format('MMM D, YYYY'));
                }else{
                    timeRange.find('span').
                        html(start.format('MMM D, YYYY') + ' - ' +
                            end.format('MMM D, YYYY')); 
                }
                    
            } else {
                timeRange.val('');
                timeRange.find('span').html('');
            }
        };

        // setting the date into the element
        cb(start, end);

        // instantiate the plugin
        timeRange.daterangepicker({
            startDate: start,
            endDate: end,
            opens: 'left',
            showDropdowns: true,
            autoUpdateInput: false,
            locale:{
                customRangeLabel: Lang.get('messages.common.custom'),
                applyLabel:Lang.get('messages.common.apply'),
                cancelLabel: Lang.get('messages.common.cancel'),
                fromLabel:Lang.get('messages.common.from'),
                toLabel: Lang.get('messages.common.to'),
                monthNames: [
                    Lang.get('messages.months.jan'),
                    Lang.get('messages.months.feb'),
                    Lang.get('messages.months.mar'),
                    Lang.get('messages.months.apr'),
                    Lang.get('messages.months.may'),
                    Lang.get('messages.months.jun'),
                    Lang.get('messages.months.jul'),
                    Lang.get('messages.months.aug'),
                    Lang.get('messages.months.sep'),
                    Lang.get('messages.months.oct'),
                    Lang.get('messages.months.nov'),
                    Lang.get('messages.months.dec')
                ],
                daysOfWeek: [
                    Lang.get('messages.weekdays.sun'),
                    Lang.get('messages.weekdays.mon'),
                    Lang.get('messages.weekdays.tue'),
                    Lang.get('messages.weekdays.wed'),
                    Lang.get('messages.weekdays.thu'),
                    Lang.get('messages.weekdays.fri'),
                    Lang.get('messages.weekdays.sat')
                ],
            },
            ranges: {
                [Lang.get('messages.days.today')]: [moment(), moment()],
                [Lang.get('messages.days.this_week')]: [
                    moment().startOf('week'),
                    moment().endOf('week')],
                [Lang.get('messages.days.last_week')]: [
                    moment().startOf('week').subtract(7, 'days'),
                    moment().startOf('week').subtract(1, 'days')],
                [Lang.get('messages.days.this_month')]: [start, end],
                [Lang.get('messages.days.last_month')]: [
                    lastMonth.clone().startOf('month'),
                    lastMonth.clone().endOf('month')],
            },
        }, cb);

        // this will fire the daterangepicker plugin change when the date has been changed
        timeRange.on('apply.daterangepicker', function (ev, picker) {
            if (picker.startDate._isValid && picker.endDate._isValid) {
                if (languageName == 'ar') {
                    $(this).val(picker.startDate.lang('en').format('YYYY-MM-DD') + ' - ' +
                        picker.endDate.lang('en').format('YYYY-MM-DD'));
                }else{
                    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' +
                        picker.endDate.format('YYYY-MM-DD'));     
                }
                tbl.ajax.reload();
            } else {
                $(this).val('');
                tbl.ajax.reload();
            }
        });

        let tbl = $('#taskTimeEntryTable').DataTable({
            language: {
                'paginate': {
                    'previous': '<i class="fas fa-angle-left"></i>',
                    'next': '<i class="fas fa-angle-right"></i>',
                },
            },
            processing: true,
            serverSide: true,
            'order': [[6, 'desc']],
            ajax: {
                url: route('task-time-entry'),
                data: function (data) {
                    data.filter_activity = $('#filterActivity').
                        find('option:selected').
                        val();
                    data.filter_date = timeRange.val();  // this will take the value directly from the daterangepicker plugin instance
                    data.taskID = taskId;
                },
            },
            columnDefs: [
                {
                    'targets': [7],
                    'orderable': false,
                },
                {
                    'targets': [2, 3],
                    'className': 'column-width',
                    'width': '15%',
                },
                {
                    'targets': [0],
                    'width': '5%',
                },
                {
                    'targets': [4],
                    'className': 'text-center',
                    'width': '10%',
                },
                {
                    'targets': [5],
                    'className': 'text-center',
                    'width': '8%',
                },
                {
                    'targets': [7],
                    'orderable': false,
                    'className': 'text-center',
                    'width': '8%',
                },
                {
                    'targets': [6],
                    'width': '11%',
                },
            ],
            columns: [
                {
                    className: 'details-control',
                    data: function (row) {
                        let todayDate = moment().
                            subtract(0, 'days').
                            format('DD-MM-YYYY');
                        let isEmpty = (row.note == 'N/A')
                            ? 'empty-details'
                            : '';
                        let todayCreatedDate = moment(row.created_at).
                            format('DD-MM-YYYY');
                        let isTodayNote = row.note == 'N/A' &&
                        todayCreatedDate == todayDate ? ' today-notice ' : '';
                        return '<a class=\'btn  btn-success collapse-icon action-btn btn-sm ' +
                            isTodayNote + isEmpty +
                            '\'><span class=\'fa fa-plus-circle action-icon\'></span></a>';
                    },
                    orderable: false,
                    searchable: false,
                },
                {
                    data: function (row){
                        let element = document.createElement('textarea');
                        element.innerHTML = row.activity_type.name;
                        return element.value;
                    },
                    name: 'activityType.name',
                },
                {
                    data: 'start_time',
                    name: 'start_time',
                },
                {
                    data: 'end_time',
                    name: 'end_time',
                },
                {
                    data: function (row) {
                        return roundToQuarterHourAll(row.duration);
                    },
                    name: 'duration',
                },
                {
                    data: function (row) {
                        return row;
                    },
                    render: function (row) {
                        if (row.entry_type == 1) {
                            return '<span class="badge badge-primary">' +
                                row.entry_type_string + '</span>';
                        }
                        return '<span class="badge badge-light">' +
                            row.entry_type_string + '</span>';
                    },
                    name: 'entry_type',
                },
                {
                    data: function (row) {
                        return row;
                    },
                    render: function (row) {
                        return '<span data-toggle="tooltip" title="' +
                            format(row.created_at, 'hh:mm:ss a') + '">' +
                            format(row.created_at) + '</span>';
                    },
                    name: 'created_at',
                },
                {
                    data: function (row) {
                        return '<a title="Edit" class="btn-edit editTaskTimeEntry task-time-entry-action mr-1" data-id="' +
                            row.id + '" data-project-id="' +
                            row.task.project_id + '">' +
                            '<i class="fas fa-edit mr-0 card-edit-icon"></i>' +
                            '</a>' +

                            '<a title="Delete" class="btn-delete cursor task-time-entry-action" data-id="' +
                            row.id + '" >' +
                            '<i class="fas fa-trash card-delete-icon"></i></a>';
                    }, name: 'id',
                },
            ],
            'fnInitComplete': function () {
                $('#filterActivity').change(function () {
                    tbl.ajax.reload();
                });
            },
            'drawCallback': function () {
                let todayNoticeLength = $('.empty-details').length;
                if (todayNoticeLength) {
                    $('div.notice').
                        html(
                            '<i class="fa fa-circle pr-1"></i><b>The Description Is Missing</b>').
                        show();
                } else {
                    $('div.notice').
                        html(
                            '<i class="fa fa-circle pr-1"></i><b>The Description Is Missing</b>').
                        hide();
                }
            },
        });

        $('#taskTimeEntryTable tbody').off('click', 'tr td.details-control');

        $('#taskTimeEntryTable tbody').on('click', 'tr td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = tbl.row(tr);
            let element = document.createElement('textarea');
            element.innerHTML = row.data().note;
            if (row.child.isShown()) {
                $(this).children().children().removeClass('fa-minus-circle').addClass('fa-plus-circle');
                row.child.hide();
                tr.removeClass('shown');
            } else {
                $(this).children().children().removeClass('fa-plus-circle').addClass('fa-minus-circle');
                row.child('<div class="padding-left-80px">' +
                    nl2br(element.value) +
                    '</div>').show();
                tr.addClass('shown');
            }
        });
    }
    $('#timeEntryTable').on('draw.dt', function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    $(document).on('click', '.addNewTimeEntry', function () {
        $('#timeEntryAddModal').appendTo('body').modal('show');
    });

    let fieldDisable = true;
    $(document).on('submit', '#timeEntryAddForm', function (event) {
        event.preventDefault();
        $('#taskId').removeAttr('disabled', false);
        $('#timeUserId').prop('disabled', false);
        $('#timeProjectId').prop('disabled', false);
        if (canManageEntries && !fieldDisable) {
            if (isEmpty($('#timeUserId').val())) {
                displayErrorMessage(
                    loginUserName + ' is not assigned to the task.');
                $('#timeUserId').prop('disabled', true);
                $('#taskId').prop('disabled', true);
                $('#timeProjectId').prop('disabled', true);
                return false;
            }
        }
        const loadingButton = jQuery(this).find('#btnSave');
        loadingButton.button('loading');
        $.ajax({
            url: route('time-entries.store'),
            type: 'POST',
            data: $(this).serialize(),
            success: function (result) {
                if (result.success) {
                    $('#timeEntryAddModal').modal('hide');
                    displaySuccessMessage('Time Entry created successfully.');
                    $('#timeEntryTable').DataTable().ajax.reload(null, false);
                    $('#taskTimeEntryTable').
                        DataTable().
                        ajax.
                        reload(null, false);
                    if (URL.length < 7) {
                        window.livewire.emit('refresh');
                    }
                }
            },
            error: function (result) {
                printErrorMessage('#tmAddValidationErrorsBox', result);
                if (!fieldDisable) {
                    $('#timeUserId').prop('disabled', true);
                    $('#timeProjectId').prop('disabled', true);
                    $('#taskId').prop('disabled', true);
                }
            },
            complete: function () {
                loadingButton.button('reset');
            },
        });
    });

    $('#timeEntryAddModal').on('hidden.bs.modal', function () {
        isEdit = false;
        $('#startTime').data('DateTimePicker').date(null);
        $('#endTime').data('DateTimePicker').date(null);
        $('#taskId').val('').trigger('change');
        $('#timeUserId').val('').trigger('change');
        $('#activityTypeId').val('').trigger('change');
        $('#duration').prop('disabled', false);
        $('#startTime').prop('disabled', false);
        $('#endTime').prop('disabled', false);
        $('#timeUserId').prop('disabled', false);
        $('#timeProjectId').prop('disabled', false);
        $('#taskId').prop('disabled', false);
        resetModalForm('#timeEntryAddForm', '#tmAddValidationErrorsBox');
    });

    $('#startTime,#endTime').on('dp.change', function () {
        const startTime = $('#startTime').val();
        const endTime = $('#endTime').val();
        let minutes = 0;
        if (endTime) {
            const diff = new Date(Date.parse(endTime) - Date.parse(startTime));
            minutes = diff / (1000 * 60);
            if (!Number.isInteger(minutes)) {
                minutes = minutes.toFixed(2);
            }
        }
        $('#duration').val(minutes).prop('disabled', true);
    });

    $('#startTime').attr('placeholder', 'YYYY-MM-DD HH:MM:SS');
    $('#endTime').attr('placeholder', 'YYYY-MM-DD HH:MM:SS');

    $(document).on('click', '#dvStartTime,#dvEndTime', function () {
        $('#startTime').removeAttr('disabled');
        $('#endTime').removeAttr('disabled');
        $('#duration').prop('disabled', true);
    });

    $('#editStartTime,#editEndTime').on('dp.change', function () {
        const startTime = $('#editStartTime').val();
        const endTime = $('#editEndTime').val();
        let minutes = 0;
        if (endTime) {
            const diff = new Date(Date.parse(endTime) - Date.parse(startTime));
            minutes = diff / (1000 * 60);
            if (!Number.isInteger(minutes)) {
                minutes = minutes.toFixed(2);
            }
        }
        $('#editDuration').val(minutes).prop('disabled', true);
        $('#editStartTime').
            data('DateTimePicker').
            maxDate(moment().endOf('now'));
        $('#editEndTime').data('DateTimePicker').maxDate(moment().endOf('now'));
    });

    $('#startTime,#editStartTime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        useCurrent: true,
        locale: languageName == 'ar' ? 'en' : languageName,
        icons: {
            up: 'icon-arrow-up icons',
            down: 'icon-arrow-down icons',
            previous: 'icon-arrow-left icons',
            next: 'icon-arrow-right icons',
        },
        sideBySide: true,
        maxDate: moment().endOf('day'),
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'bottom',
        },
    });

    $('#endTime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        useCurrent: true,
        locale: languageName == 'ar' ? 'en' : languageName,
        icons: {
            up: 'icon-arrow-up icons',
            down: 'icon-arrow-down icons',
            previous: 'icon-arrow-left icons',
            next: 'icon-arrow-right icons',
        },
        sideBySide: true,
        maxDate: moment().endOf('day'),
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'bottom',
        },
    });

    $('#editEndTime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        useCurrent: true,
        locale: languageName == 'ar' ? 'en' : languageName,
        icons: {
            up: 'icon-arrow-up icons',
            down: 'icon-arrow-down icons',
            previous: 'icon-arrow-left icons',
            next: 'icon-arrow-right icons',
        },
        sideBySide: true,
        maxDate: moment().endOf('day'),
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'bottom',
        },
    });

    $('#startTime, #editStartTime, #endTime, #editEndTime').
        on('dp.show', function () {
            matchWindowScreenPixels({
                startTime: '#startTime',
                editStartTime: '#editStartTime',
                endTime: '#endTime',
                editEndTime: '#editEndTime',
            }, 'tsk');
        });

    $(window).resize(function () {
        matchWindowScreenPixels({
            startTime: '#startTime',
            editStartTime: '#editStartTime',
            endTime: '#endTime',
            editEndTime: '#editEndTime',
        }, 'tsk');
    }).trigger('resize');

    $('#startTime,#endTime').on('dp.change', function (selected) {
        $('#startTime').data('DateTimePicker').maxDate(moment().endOf('now'));
        $('#endTime').data('DateTimePicker').maxDate(moment().endOf('now'));
    });

    $(document).on('submit', '#editTimeEntryForm', function (event) {
        event.preventDefault();
        $('#editTimeUserId').prop('disabled', false);
        $('#editTimeProjectId').prop('disabled', false);
        $('#editTaskId').prop('disabled', false);
        if (canManageEntries && loginUserAdmin) {
            if (isEmpty($('#editTimeUserId').val())) {
                displayErrorMessage('Please select user.');
                $('#editTimeUserId').prop('disabled', true);
                return false;
            }
        }
        const loadingButton = jQuery(this).find('#btnEditSave');
        loadingButton.button('loading');
        const id = $('#entryId').val();
        $.ajax({
            url: route('time-entries.update',id),
            type: 'put',
            data: $(this).serialize(),
            success: function (result) {
                if (result.success) {
                    $('#editTimeEntryModal').modal('hide');
                    displaySuccessMessage('Time Entry updated successfully.');
                    $('#taskTimeEntryTable').
                        DataTable().
                        ajax.
                        reload(null, false);
                    if ($.isFunction(window.taskDetails)) {
                        taskDetails(result.data.task_id);
                    }
                }
            },
            error: function (error) {
                manageAjaxErrors(error, 'teEditValidationErrorsBox');
                $('#editTimeUserId').prop('disabled', true);
                $('#editTimeProjectId').prop('disabled', true);
                $('#editTaskId').prop('disabled', true);
            },
            complete: function () {
                loadingButton.button('reset');
            },
        });
    });

    $('#editTimeEntryModal').on('hidden.bs.modal', function () {
        $('#editDuration').prop('disabled', false);
        $('#editStartTime').prop('disabled', false);
        $('#editEndTime').prop('disabled', false);
        resetModalForm('#editTimeEntryForm', '#teEditValidationErrorsBox');
    });

    let timeUserId = '';
    window.renderTimeEntry = function (id) {
        $.ajax({
            url: route('time-entries.edit',id),
            type: 'GET',
            success: function (result) {
                if (result.success) {
                    let timeEntry = result.data;
                    let element = document.createElement('textarea');
                    element.innerHTML = timeEntry.note;
                    timeUserId = timeEntry.user_id;
                    editTaskId = timeEntry.task_id;
                    editProjectId = timeEntry.project_id;
                    $('#entryId').val(timeEntry.id);
                    $('#editTimeProjectId').
                        val(timeEntry.project_id).
                        trigger('change');
                    $('#editActivityTypeId').
                        val(timeEntry.activity_type_id).
                        trigger('change');
                    $('#editDuration').val(timeEntry.duration);
                    $('#editStartTime').val(timeEntry.start_time);
                    $('#editEndTime').val(timeEntry.end_time);
                    $('#editNote').val(element.value);
                    $('#editTimeEntryModal').appendTo('body').modal('show');
                    $('#editTimeUserId').prop('disabled', true);
                    $('#editTimeProjectId').prop('disabled', true);

                    //add it cause of project_id change, when it change it sets tasks dynamically and selected task_id vanished
                    setTimeout(function () {
                        $('#editTaskId').
                            val(editTaskId).
                            trigger('change.select2'),
                            $('#editTaskId').prop('disabled', false);
                    }, 1000);

                    setTimeout(function () {
                        $('#editTimeUserId').
                            val(timeEntry.user_id).
                            trigger('change.select2');
                    }, 1000);
                }
            },
            error: function (error) {
                displayErrorMessage(error.responseJSON.message)
                manageAjaxErrors(error, 'teEditValidationErrorsBox');
            },
        });
    };

    $(document).on('click', '.btn-edit', function (event) {
        let timeId = $(event.currentTarget).attr('data-id');
        renderTimeEntry(timeId);
    });

    $(document).on('click', '.btn-delete', function (event) {
        let timeId = $(event.currentTarget).attr('data-id');
        deleteItem(route('time-entries.destroy', timeId), '#timeEntryTable', 'Time Entry');
    });

    $(document).on('click', '.btn-task-time-note', function (event) {
        let timeId = $(event.currentTarget).attr('data-id');
        $.ajax({
            url: route('showTimeEntryNote',timeId),
            type: 'GET',
            success: function (result) {
                if (result.success) {
                    $('#showNote').html('');
                    if (!isEmpty(result.data.note)) {
                        let element = document.createElement('textarea');
                        element.innerHTML = result.data.note;
                        $('#showNote').append(element.value.replace(/\n/g, '<br/>'));
                    } else {
                        $('#showNote').append('N/A');
                    }
                    $('#showModal').appendTo('body').modal('show');
                }
            },
            error: function (result) {
                displayErrorMessage(result.responseJSON.message);
            },
        });
    });

    window.getTasksByProject = function (
        projectId, taskId, selectedId, errorBoxId) {
        if (!(projectId > 0)) {
            return false;
        }
        let taskURL = route('project-tasks',projectId);
        if (!isEmpty(timeUserId)) {
            taskURL = (isEdit) ? taskURL + '?task_id=' + editTaskId : taskURL +
                '?user_id=' + timeUserId;
        } else {
            taskURL = (isEdit) ? taskURL + '?task_id=' + editTaskId : taskURL;
        }

        $.ajax({
            url: taskURL,
            type: 'get',
            success: function (result) {
                var tasks = result.data;
                if (selectedId > 0) {
                    var options = '<option value="0" disabled>Select Task</option>';
                } else {
                    var options = '<option value="0" disabled selected>Select Task</option>';
                }
                $.each(tasks, function (key, value) {
                    selectedId = key;
                    if (selectedId > 0 && selectedId == key) {
                        options += '<option value="' + key + '" selected>' +
                            value +
                            '</option>';
                    } else {
                        options += '<option value="' + key + '">' + value +
                            '</option>';
                    }
                });
                $(taskId).html(options);
                if (selectedId > 0) {
                    $(taskId).val(selectedId).trigger('change');
                }
            },
            error: function (result) {
                printErrorMessage(errorBoxId, result);
            },
        });
    };

    $('#timeProjectId').on('change', function () {
        $('#taskId').select2('val', '');
        const projectId = $(this).val();
        getTasksByProject(projectId, '#taskId', 0, '#tmAddValidationErrorsBox');
    });

    $('#editTimeProjectId').on('change', function () {
        $('#editTaskId').select2('val', '');
        const projectId = $(this).val();
        isEdit = (editProjectId == projectId) ? true : false;

        getTasksByProject(projectId, '#editTaskId', 0,
            '#teEditValidationErrorsBox');
    });

    $(document).on('click', '#new_entry', function () {
        var tracketProjectId = localStorage.getItem('project_id');
        $('#timeProjectId').val(tracketProjectId);
        $('#timeProjectId').trigger('change');
        getTasksByProject(tracketProjectId, '#taskId', 0,
            '#tmAddValidationErrorsBox');
        $('#endTime').val(moment().format('YYYY-MM-DD HH:mm:ss'));
    });

// event to copy today time entries
    $(document).on('click', '#copyTodayEntry', function () {
        screenLock();
        $.ajax({
            url: route('copy-today-activity'),
            type: 'get',
            success: function (result) {
                let element = document.createElement('textarea');
                element.innerHTML = result;
                copyTextToClipBoard(element.value);
                swal({
                    title: 'Copied',
                    text: 'Time Entries copied to clipboard.',
                    type: 'success',
                    timer: 3000,
                    confirmButtonColor: '#6777EF',
                });
                screenUnLock();
            },
            error: function (result) {
                printErrorMessage('#tmValidationErrorsBox', result);
            },
        });
    });

// function to copy text to clipboard
    window.copyTextToClipBoard = function (resultData) {
        let copyFrom = document.createElement('textarea');
        document.body.appendChild(copyFrom);
        copyFrom.textContent = resultData;
        copyFrom.select();
        document.execCommand('copy');
        copyFrom.remove();
    };

    window.getProjectsByUser = function (
        userId, projectId, taskId, selectedId, errorBoxId) {
        if (!(userId > 0)) {
            return false;
        }

        $.ajax({
            url: route('users-project',userId),
            type: 'get',
            success: function (result) {
                const projects = result.data;
                let options = '<option value="0" disabled selected>Select Project</option>';
                if (selectedId > 0) {
                    options = '<option value="0" disabled>Select Project</option>';
                }
                $.each(projects, function (key, value) {
                    if (selectedId > 0 && selectedId == key) {
                        options += '<option value="' + key + '" selected>' +
                            value +
                            '</option>';
                    } else {
                        options += '<option value="' + key + '">' + value +
                            '</option>';
                    }
                });
                $(projectId).html(options);
                $(taskId).html('');
                if (selectedId > 0) {
                    $(projectId).val(selectedId).trigger('change');
                }
            },
            error: function (result) {
                printErrorMessage(errorBoxId, result);
            },
        });
    };

    $('#timeUserId').on('change', function () {
        $('#taskId').select2('val', '');
        $('#timeProjectId').select2('val', '');
        const userId = $(this).val();
        timeUserId = $(this).val();
        getProjectsByUser(userId, '#timeProjectId', '#taskId', 0,
            '#tmAddValidationErrorsBox');
    });

    $('#editTimeUserId').on('change', function () {
        $('#editTaskId').select2('val', '');
        $('#editTimeProjectId').select2('val', '');
        const userId = $(this).val();
        timeUserId = $(this).val();
        getProjectsByUser(userId, '#editTimeProjectId', '#editTaskId', 0,
            '#teEditValidationErrorsBox');
    });

    $(document).on('click', '#task-time-entry-note', function () {
    });

    $(document).on('click', '.taskTimeTracking', function () {
        $('#timeTrackingModal').appendTo('body').modal('show');
    });

    if (!canManageEntries) {
        $(document).on('click', '.addTaskTimeEntry', function () {
            $('#endTime').val(moment().format('YYYY-MM-DD HH:mm:ss'));
            fieldDisable = false;
            $('#timeUserId').prop('disabled', true);
            $('#timeProjectId').prop('disabled', true);
            setTimeout(function () {
                $('#taskId').prop('disabled', false);
            }, 1000);
        });
    }

    if (canManageEntries) {
        $(document).on('click', '.addTaskTimeEntry', function (event) {
            $('#endTime').val(moment().format('YYYY-MM-DD HH:mm:ss'));
            fieldDisable = false;
            let projectId = $(event.currentTarget).attr('data-project-id');
            $('#timeUserId').empty();
            getUserByTask(projectId, '#timeUserId');
        });

        $(document).on('click', '.editTaskTimeEntry', function (event) {
            let projectId = $(event.currentTarget).attr('data-project-id');
            $('#editTimeUserId').empty();
            getUserByTask(projectId, '#editTimeUserId');
        });

        window.getUserByTask = function (projectId, userId) {
            if (!(projectId > 0)) {
                return false;
            }
            
            $.ajax({
                url: route('project-users',projectId),
                type: 'GET',
                success: function (result) {
                    var tasks = result.data;
                    $.each(result.data, function (i, v) {
                        let element = document.createElement('textarea');
                        element.innerHTML = v;
                        $(userId).
                            append($('<option></option>').
                                attr('value', i).
                                text(element.value));
                    });
                    $('#timeUserId').
                        val(loggedInUserId).
                        trigger('change.select2');
                    $('#timeUserId').prop('disabled', true);
                    $('#timeProjectId').prop('disabled', true);
                    setTimeout(function () {
                        $('#taskId').prop('disabled', false);
                    }, 1000);
                },
            });
        };
    }

    $(document).on('click', '.manuallyTaskTimeEntry', function (event) {
        $('#endTime').val(moment().format('YYYY-MM-DD HH:mm:ss'));
        fieldDisable = true;
        $('#timeUserId').empty();
        $.ajax({
            url: route('get-user-lists'),
            type: 'GET',
            success: function (result) {
                if (result.data.endTime != null){
                    let startTime = moment(result.data.endTime).add(5,'seconds').format('YYYY-MM-DD HH:mm:ss');
                    $('#startTime').data("DateTimePicker").date(startTime);
                }
                if (canManageEntries) {
                    $('#timeUserId').
                        find('option').
                        remove().
                        end().
                        append('<option value="">Select User</option>');
                    $.each(result.data.users, function (i, v) {
                        $('#timeUserId').
                            append($('<option></option>').
                                attr('value', i).
                                text(v));
                    });
                    $('#timeUserId').val(loginUserId).trigger('change');
                } else {
                    return true;
                }
            },
        });
    });
});
