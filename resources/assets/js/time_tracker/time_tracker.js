'use strict';

let firstHour = 1;
let firstMinute = 0;
let secondHour =1;
let secondMinute = 10;
let thirdHour = 1;
let thirdMinute = 15;
$(document).ready(function () {
    moment.locale('en')
    $('#tmActivityId,#tmProjectId').select2({
        width: '100%',
    });
    $('#tmTaskId').select2({
        width: 'calc(100% - 44px)',
    });
    $('#trackerTaskProjectId').select2({
        placeholder: 'Select Project',
        width: '100%',
    });
    if ($('#startTimer').css('display') == 'none') {
        $('.img-stopwatch').attr('src', timer);
    } else {
        $('.img-stopwatch').
            attr('src', stopWatchImg);
    }

        let userId = loggedInUserId;
        $.ajax({
            url: route('notifications.index'),
            type:'GET',
            success: function (result) {
                if (!isEmpty(result.data) && result.success) {
                    let firstHourMinute = result.data.first_notification_hour.split(
                        ':');
                    if (firstHourMinute[0][0] == 0) {
                        firstHour = firstHourMinute[0][1];
                    } else {
                        firstHour = firstHourMinute[0];
                    }
                    if (firstHourMinute[1][0] == 0) {
                        firstMinute = firstHourMinute[1][1];
                    } else {
                        firstMinute = firstHourMinute[1];
                    }

                    let secondHourMinute = result.data.second_notification_hour.split(
                        ':');
                    if (secondHourMinute[0][0] == 0) {
                        secondHour = secondHourMinute[0][1];
                    } else {
                        secondHour = secondHourMinute[0];
                    }
                    if (secondHourMinute[1][0] == 0) {
                        secondMinute = secondHourMinute[1][1];
                    } else {
                        secondMinute = secondHourMinute[1];
                    }

                    let thirdHourMinute = result.data.third_notification_hour.split(
                        ':');
                    if (thirdHourMinute[0][0] == 0) {
                        thirdHour = thirdHourMinute[0][1];
                    } else {
                        thirdHour = thirdHourMinute[0];
                    }
                    if (secondHourMinute[1][0] == 0) {
                        thirdMinute = thirdHourMinute[1][1];
                    } else {
                        thirdMinute = thirdHourMinute[1];
                    }
                }
            },
            error: function (result) {
                UnprocessableInputError(result);
            },
        });
});

let lastProjectId = null;
window.loadProjects = function () {
    $.ajax({
        url: route('my-projects'),
        type: 'GET',
        success: function (result) {
            let element = document.createElement('textarea');
            $('#tmProjectId').
                find('option').
                remove().
                end().
                append('<option value="">Select Project</option>')
            $(result.data).each(function (i, e) {
                element.innerHTML = e.name;
                $('#tmProjectId').
                    append($('<option></option>').
                        attr('value', e.id).
                        text(element.value))
            })
            if (getItemFromLocalStorage('clockRunning') !== null) {
                lastProjectId = getItemFromLocalStorage('project_id')
                $('#tmProjectId').val(lastProjectId).trigger('change')
                $('#tmProjectId').attr('disabled', true)
            }
        },
    })
}

loadProjects()
let isClockRunning = getItemFromLocalStorage('clockRunning')
$(window).on('load', function () {
    if (isClockRunning == null) {
        getUserLastTaskWork()
        $('#addTaskTracker').show();
    }
})

window.revokerTracker = function () {
    loadProjects()
    if(!isEmpty()) {
        setTimeout(function () {
            $('#tmProjectId').val(lastProjectId).trigger('change')
        }, 1500)
    }
}

window.showStartTimeButton = function () {
    $('#stopTimer').hide()
    $('#timer').html('<h3><b>00:00:00</b></h3>')
    $('#startTimer').show()
}

window.startWatch = function () {
    if (getItemFromLocalStorage('clockRunning') == null) {
        showStartTimeButton()
        return
    }
    $('#startTimer').hide()
    $('#stopTimer').show()

    var stTime = (getItemFromLocalStorage('start_time') !== null)
        ? getItemFromLocalStorage('start_time')
        : getCurrentTime()
    var d1 = new Date($.now());
    var d2 = new Date(moment(stTime).format('YYYY-MM-DD HH:mm:ss'));
    var diffMs = parseInt(d1 - d2);
    hours = parseInt((diffMs / (1000 * 60 * 60)) % 24);
    minutes = parseInt((diffMs / (1000 * 60)) % 60);
    seconds = parseInt((diffMs / 1000) % 60);

    gethours = (hours < 10) ? ('0' + hours + ': ') : (hours + ': ');
    mins = (minutes < 10) ? ('0' + minutes + ': ') : (minutes + ': ');
    secs = (seconds < 10) ? ('0' + seconds) : (seconds);

    if (localStorage.getItem('hours_' + loggedInUserId) == firstHour &&
        localStorage.getItem('minutes_' + loggedInUserId) == firstMinute &&
        localStorage.getItem('seconds_' + loggedInUserId) ==
        parseInt('1')) {
        if (firstHour != '0' || firstMinute != '0') {
            Push.create('Hello ' + loggedInUserName, {
                body: 'Your tracker time limit is exceed. Please stop your tracker.',
                icon: notificationImg,
                timeout: 5000,
                url: notificationUrl,
                onClick: function () {
                    window.focus();
                    // this.close();
                },
            });
            // $.ajax({
            //     url: route('tracker.notification.command'),
            //     type: 'get',
            //     data: {},
            //     success: function (result) {
            //
            //     },
            // });
        }
    } else if (localStorage.getItem('hours_' + loggedInUserId) == secondHour &&
        localStorage.getItem('minutes_' + loggedInUserId) == secondMinute &&
        localStorage.getItem('seconds_' + loggedInUserId) == parseInt('1')) {
        Push.create('Hello ' + loggedInUserName, {
            body: 'Your tracker time limit is exceed. Please stop your tracker.',
            icon: notificationImg,
            timeout: 5000,
            url: notificationUrl,
            onClick: function () {
                window.focus();
                // this.close();
            },
        });
        // $.ajax({
        //     url: route('tracker.notification.command'),,
        //     type: 'get',
        //     data: {},
        //     success: function (result) {
        //
        //     },
        // });
    } else if(localStorage.getItem('hours_'+loggedInUserId) == thirdHour && localStorage.getItem('minutes_'+loggedInUserId) == thirdMinute && localStorage.getItem('seconds_'+loggedInUserId) == parseInt('1')){
        Push.create('Hello '+loggedInUserName, {
            body: 'Your tracker time limit is exceed. Please stop your tracker.',
            icon: notificationImg,
            timeout: 5000,
            url: notificationUrl,
            onClick: function () {
                window.focus();
                // this.close();
            },
        });
        // $.ajax({
        //     url: route('tracker.notification.command'),
        //     type: 'get',
        //     data: {},
        //     success: function (result) {
        //     },
        // });
    }
    // display the stopwatch
    $('#timer').html('<h3><b>' + gethours + mins + secs + '</b></h3>')
    seconds++

    setItemToLocalStorage(
        { 'seconds': seconds, 'minutes': minutes, 'hours': hours })
    clearTime = setTimeout('startWatch( )', 1000)
}

window.stopWatch = function () {
    clear = setTimeout('stopWatch( )', 1000)
}


var isOpen = 0

$(document).on('click', '#imgTimer', function () {
    if ($('#timeTracker').is(':hidden')) {
        $('#timeTracker').show();
        $('.img-stopwatch').
            attr('src', closeWatchImg);
    } else {
        $('#timeTracker').hide();
        if ($('#startTimer').css('display') == 'none') {
            $('.img-stopwatch').attr('src', timer);
        } else {
            $('.img-stopwatch').
                attr('src', stopWatchImg);
        }
    }
    $('#validationErrorsBox').hide();
})

// if timer is running then set values as it is
if (getItemFromLocalStorage('clockRunning') !== null) {
    startWatch()
}

$('#drpUsers,#drpActivity,#drpTasks').select2({
    width: '100%',
})

var clear

// initialize your variables outside the function
var clearTime
var count, seconds = 0, minutes = 0, hours = 0
var secs, mins, gethours
var entryStartTime, entryStopTime = 0

function startTimerEvent () {
    $.ajax({
        url: route('start-timer'),
        type: 'post',
        data: {
            'activity': $('#tmActivityId').val(),
            'task': $('#tmTaskId').val(),
            'project': $('#tmProjectId').val(),
        },
        success: function () {

        },
        error: function (result) {
            printErrorMessage('#timeTrackerValidationErrorsBox', result)
        },
    })
}
var currentTaskId;
var currentActivityId;
$(document).on('click', '#startTimer', function (e) {
    var activity = $('#tmActivityId').val();
    var task = $('#tmTaskId').val();
    var project = $('#tmProjectId').val();
    if (project != '' && activity != '' && (task != '' && !(task == null))) {
        e.preventDefault();
        currentTaskId = localStorage.setItem('currentTaskId', task);
        currentActivityId = localStorage.setItem('currentActivityId', activity);
        $('#stopTimer').removeAttr('disabled');
        setTimerData(activity, task, project);
        startTimerEvent();
    }
});

function setTimerData (activity, task, project) {
    $('#tmActivityId').attr('disabled', true)
    $('#tmTaskId').attr('disabled', true)
    $('#tmProjectId').attr('disabled', true)

    var setItems = {
        'user_id': loggedInUserId,
        'activity_id': activity,
        'task_id': task,
        'project_id': project,
        'clockRunning': true,
    }
    setItemToLocalStorage(setItems)

    entryStartTime = getCurrentTime()
    if (getItemFromLocalStorage('start_time') !== null) {
        entryStartTime = getItemFromLocalStorage('start_time')
    } else {
        setItemToLocalStorage({ 'start_time': entryStartTime })
    }
    startWatch()
}

$(document).on('click', '#stopTimer', function (e) {
    e.preventDefault();
    currentTaskId = localStorage.removeItem('currentTaskId');
    currentActivityId = localStorage.removeItem('currentActivityId');
    $(this).attr('disabled', 'true');
    enableTimerData();

    $('#loader').show();
    checkTimeEntry();
});

function enableTimerData () {
    $('#tmActivityId').removeAttr('disabled');
    $('#tmTaskId').removeAttr('disabled');
    $('#tmProjectId').removeAttr('disabled');
    $('#tmNotes').html('');
    $('#tmNotesErr').html('');

    stopTime();
}

//create a function to start the stop watch
function startTime () {
    /* check if seconds, minutes, and hours are equal to zero and start the stop watch */
    if (seconds == 0 && minutes == 0 && hours == 0) {
        startWatch()
    }
}

function stopTime () {
    seconds = minutes = hours = 0
}

function diff_mins (dt2, dt1) {
    dt2 = new Date(dt2)
    dt1 = new Date(dt1)
    var diff = (dt2.getTime() - dt1.getTime()) / 1000
    diff /= (60)
    return Math.abs(Math.round(diff))
}

function adjustTimeEntry () {
    let startDate = getItemFromLocalStorage('start_time')
    $('#tmAdjustValidationErrorsBox').show()
    $('#tmAdjustValidationErrorsBox').
        html('Time Entry must be less than 12 hours.')
    $('#adjustStartTime').val(startDate)
    $('#adjustStartTime').attr('disabled', 'true')
    $('#timeEntryAdjustModal').modal()
    $('#stopTimer').removeAttr('disabled')
}

$('#timeEntryAdjustModal').on('hidden.bs.modal', function () {
    $('#adjustEndTime').prop('disabled', false)
    $('#adjustStartTime').prop('disabled', false)
    $('#adjustEndTime').data('DateTimePicker').date(null)
    $('#adjustStartTime').data('DateTimePicker').date(null)
    resetModalForm('#timeEntryAdjustForm')
    $('#tmAdjustValidationErrorsBox').hide()
})

$('#adjustStartTime').datetimepicker({
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
})
$('#adjustEndTime').datetimepicker({
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
})

$('#adjustStartTime,#adjustEndTime').on('dp.change', function () {
    const startTime = $('#adjustStartTime').val()
    const endTime = $('#adjustEndTime').val()
    let minutes = 0
    if (endTime) {
        const diff = new Date(Date.parse(endTime) - Date.parse(startTime))
        minutes = diff / (1000 * 60)
        if (!Number.isInteger(minutes)) {
            minutes = minutes.toFixed(2)
        }
    }
    $('#adjustDuration').val(minutes).prop('disabled', true);
    $('#adjustStartTime').data('DateTimePicker').maxDate(moment().endOf('now'));
    $('#adjustEndTime').data('DateTimePicker').maxDate(moment().endOf('now'));
    if (minutes < 720.00) {
        $('#tmAdjustValidationErrorsBox').hide();
    }
});

$(document).on('click', '#adjustBtnSave', function () {
    let startTime = $('#adjustStartTime').val();
    let endTime = $('#adjustEndTime').val();
    let totalMin = diff_mins(endTime, startTime);
    if (totalMin > 720) {
        $('#tmAdjustValidationErrorsBox').show();
        $('#tmAdjustValidationErrorsBox').
            html('Time Entry must be less than 12 hours.');
    } else {
        $('#adjustBtnCancel').trigger('click');
        storeTimeEntry(startTime, endTime);
    }
});

function checkTimeEntry () {
    let startTime = getItemFromLocalStorage('start_time')
    let endTime = getCurrentTime()
    let totalMin = diff_mins(endTime, startTime)
    if (totalMin > 720) {
        adjustTimeEntry()
    } else {
        storeTimeEntry(startTime, endTime)
    }
}

function storeTimeEntry (startTime, endTime) {
    $.ajax({
        url: route('time-entries.store'),
        type: 'POST',
        data: $('#timeTrackerForm').serialize() + '&start_time=' + startTime +
            '&end_time=' + endTime,
        success: function (result) {
            if (result.success) {
                stopLoader();
                $('#loader').hide();
                swal({
                    'title': 'Success',
                    'text': 'Time Entry stored successfully!',
                    'type': 'success',
                    'confirmButtonColor': '#6777EF',
                    'timer': 1000,
                });
                if (url == '/tasks') {
                    window.livewire.emit('refresh');
                }
                stopTimerData();

                $('#taskTimeEntryTable').
                    DataTable().
                    ajax.
                    reload(null, false);

                let urlPathName = window.location.pathname;
                if (urlPathName == '/dashboard') {
                    // window.location.reload();
                    const today = moment();
                    let start = today.clone().startOf('month');
                    let end = today.clone().endOf('month');
                    loadUserWorkReport(start.format('YYYY-MM-D  H:mm:ss'),
                        end.format('YYYY-MM-D  H:mm:ss'), loggedInUserId);
                    loadDevelopersWorkReport(today.format('YYYY-MM-D  H:mm:ss'))
                }

                $('#timeTracker').hide();
                $('.img-stopwatch').attr('src', stopWatchImg);
                $('#tmNotes').val('');
                localStorage.removeItem('notes' + '_' + loggedInUserId);

            }
        },
        error: function (result) {
            stopLoader();
            printErrorMessage('#timeTrackerValidationErrorsBox', result);
            $('#tmActivityId').attr('disabled', true);
            $('#tmTaskId').attr('disabled', true);
            $('#tmProjectId').attr('disabled', true);
            $('#stopTimer').removeAttr('disabled');
            let selectedTask = $('#timeTrackerForm').find('#tmTaskId').val();
            if (!(selectedTask > 0)) {
                $('#tmTaskId').prop('disabled', false);
            }
        },
        complete: function () {
        },
    })
}

function stopTimerData () {
    stopWatch()
    $('#stopTimer').hide()
    $('#timer').html('<h3><b>00:00:00</b></h3>')
    $('#startTimer').show()
    clearTimeout(clearTime)

    var removeItems = [
        'user_id',
        'activity_id',
        'task_id',
        'clockRunning',
        'start_time',
        'seconds',
        'minutes',
        'hours',
        'notes'];
    removeItemsFromLocalStorage(removeItems)
}

function getCurrentTime (datetime = null) {
    var dt = (datetime === null) ? new Date($.now()) : new Date(datetime)
    var date = (dt.getDate() < 10) ? '0' + dt.getDate() : dt.getDate()
    var month = ((dt.getMonth() + 1) < 10)
        ? ('0' + (dt.getMonth() + 1))
        : (dt.getMonth() + 1)
    var hours = (dt.getHours() < 10) ? '0' + dt.getHours() : dt.getHours()
    var minutes = (dt.getMinutes() < 10)
        ? '0' + dt.getMinutes()
        : dt.getMinutes()
    var seconds = (dt.getSeconds() < 10)
        ? '0' + dt.getSeconds()
        : dt.getSeconds()

    return dt.getFullYear() + '-' + month + '-' + date + ' ' + hours + ':' +
        minutes + ':' + seconds
}

function setItemToLocalStorage (items) {
    $.each(items, function (key, value) {
        localStorage.setItem(key + '_' + loggedInUserId, value)
    })
}

function removeItemsFromLocalStorage (items) {
    $.each(items, function (index, value) {
        localStorage.removeItem(value + '_' + loggedInUserId)
    })
}

$('#tmProjectId').on('change', function (e) {
    e.preventDefault();
    $('#tmTaskId').attr('disabled', true)

    var projectId = lastProjectId = $('#tmProjectId').val()
    loadTimerData(projectId)
})

function loadTimerData (projectId) {
    $.ajax({
        url: myTasksUrl + '?project_id=' + projectId,
        type: 'GET',
        success: function (result) {
            let element = document.createElement('textarea');
            $('#tmTaskId').
                find('option').
                remove().
                end().
                append('<option value="">Select Task</option>')
            $('#tmTaskId').val('').trigger('change');

            let drpTaskId = getItemFromLocalStorage('task_id');
            let drpActivityId = getItemFromLocalStorage('activity_id');
            const taskNotes = getItemFromLocalStorage('notes');
            let isTaskEmpty = true;

            $.each(result.data.tasks, function (i, v) {
                element.innerHTML = v;
                $('#tmTaskId').
                    append($('<option></option>').attr('value', i).text(element.value));
                if (i == drpTaskId) {
                    isTaskEmpty = false;
                }
            });

            $('#tmActivityId').
                find('option').
                remove().
                end().
                append('<option value="">Select Activity</option>')
            $('#tmActivityId').val('').trigger('change')
            $(result.data.activities).each(function (i, e) {
                $('#tmActivityId').
                    append($('<option></option>').
                        attr('value', e.id).
                        text(e.name))
            })

            $('#tmTaskId').removeAttr('disabled')
            // if timer is running then set values as it is
            if (getItemFromLocalStorage('clockRunning') !== null) {
                $('#tmActivityId').val(drpActivityId).trigger('change')
                $('#tmTaskId').val(drpTaskId).trigger('change')
                $('#tmNotes').val(taskNotes);

                $('#tmTaskId').attr('disabled', true)
                $('#tmActivityId').attr('disabled', true)
            } else {
                $('#tmActivityId').val(drpActivityId).trigger('change')
                $('#tmTaskId').val(drpTaskId).trigger('change')
            }

            if (isTaskEmpty) {
                $('#tmTaskId').
                    val($('#tmTaskId option:first').val()).
                    trigger('change')
            }
        },
    })
}

function getUserLastTaskWork () {
    $.ajax({
        url: route('user-last-task-work'),
        type: 'GET',
        success: function (result) {
            if (result.success) {
                if (result.data) {
                    let lastTask = result.data
                    if (isClockRunning == null) {
                        let setItems = {
                            'user_id': loggedInUserId,
                            'activity_id': lastTask.activity_id,
                            'task_id': lastTask.task_id,
                            'project_id': lastTask.project_id,
                        }
                        setItemToLocalStorage(setItems)
                        lastProjectId = lastTask.project_id
                        $('#tmProjectId').
                            val(lastTask.project_id).
                            trigger('change')
                    }
                }
            }
        },
    })
}

$('#tmNotes').on('keyup', function () {
    setItemToLocalStorage({ 'notes': $(this).val() });
});

$(document).on('click', '#addTaskTracker', function (){
    $.ajax({
        url: route('projects.login.user'),
        type: 'GET',
        success: function (result) {
            let element = document.createElement('textarea');
            $('#trackerTaskProjectId').empty();
            $.each(result.data,function (i, e) {
                element.innerHTML = i;
                $('#trackerTaskProjectId').
                append($('<option></option>').
                attr('value', e).
                text(element.value))
            })
            $('#addTrackerTaskModal').modal('show');
        },
    })
});

$(document).on('submit', '#addTrackerTaskForm', function (event) {
    event.preventDefault();
    let loadingButton = jQuery(this).find('#btnTrackerTaskSave');
    loadingButton.button('loading');
    let formdata = $(this).serialize();
    $.ajax({
        url: '/tracker/task',
        type: 'POST',
        data: formdata,
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#addTrackerTaskModal').modal('hide');
                revokerTracker();
                if (window.location.pathname === '/tasks'){
                    window.livewire.emit('refresh');
                }
                if (window.location.pathname === '/dashboard'){
                    loadUsersOpenTasks();
                }
                loadingButton.button('reset');
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            loadingButton.button('reset')
        },
    })
})

$('#addTrackerTaskModal').on('hidden.bs.modal', function (){
    $('#addTrackerTaskForm')[0].reset()
});
