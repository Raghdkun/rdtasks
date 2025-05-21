$(document).ready(function () {
    'use strict';

    $('#editTimeUserId, #editTimeProjectId, #editTaskId, #editActivityTypeId').
        select2({
            width: '100%',
        });
    $('#calendarFilterUser').select2();
    let calendar = $('#calendar').fullCalendar({
        // themeSystem: 'bootstrap4',
        height: 750,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'agendaWeek,agendaDay',
        },
        buttonText: {
            today: 'Today',
            week: 'Week',
            day: 'Day',
        },
        defaultDate: new Date(),
        defaultView: 'agendaWeek',
        editable: false,
        allDaySlot: false,
        eventRender: function (event, element) {
            let timeDuration = 'Duration - ' +
                roundToQuarterHourAllForCalendarView(event.time_duration);
            element.find('.fc-title').append('<br>' + timeDuration);
        },
        eventAfterRender: function (event, element) {
            $(element).tooltip({
                title: event.user,
                container: 'body',
            });
        },
        timeFormat: 'h:mm A',
        eventAfterAllRender: function (view) { /* used this vs viewRender */
            setTimeout(function () {
                $('#calendar button.fc-today-button').
                    removeClass('disabled').
                    prop('disabled', false);
            }, 100);
        },
        eventClick: function (event) {
            renderTimeEntry(event.id);
        },
    });

    let start_date = moment().startOf('week').format();
    let end_date = moment().endOf('week').format();
    let userId = $('#calendarFilterUser').val();

    $(document).ready(function () {
        $('#calendarFilterUser').val(loginUserId).trigger('change');
        if (!loginUserAdmin) {
            userId = loginUserId;
            getWeekDataForCalendar(start_date, end_date, loginUserId);
        }
    });

    //Change user
    $(document).on('change', '#calendarFilterUser', function () {
        userId = $(this).val();
        screenLock();
        getWeekDataForCalendar(start_date, end_date, userId);
    });

    //Click on Prev Button
    $(document).on('click', 'button.fc-prev-button', function () {
        screenLock();
        start_date = $('#calendar').fullCalendar('getView').start.format();
        end_date = $('#calendar').fullCalendar('getView').end.format();
        getWeekDataForCalendar(start_date, end_date, userId);
    });

    //Click on Next Button
    $(document).on('click', 'button.fc-next-button', function () {
        screenLock();
        start_date = $('#calendar').fullCalendar('getView').start.format();
        end_date = $('#calendar').fullCalendar('getView').end.format();
        getWeekDataForCalendar(start_date, end_date, userId);
    });

    //Click on ToDay Button
    calendar.find('.fc-today-button').click(function () {
        start_date = $('#calendar').fullCalendar('getView').start.format();
        end_date = moment().endOf('day').format();
        getWeekDataForCalendar(start_date, end_date, userId);
    });

    //Click on Day Button
    calendar.find('.fc-agendaDay-button').click(function () {
        start_date = $('#calendar').fullCalendar('getView').start.format();
        getWeekDataForCalendar(start_date, start_date, userId);
    });

    //Click on Week Button
    calendar.find('.fc-agendaWeek-button').click(function () {
        start_date = $('#calendar').fullCalendar('getView').start.format();
        end_date = $('#calendar').fullCalendar('getView').end.format();
        let d = new Date(end_date);
        d.setDate(d.getDate() - 1);
        end_date = d.toISOString();
        getWeekDataForCalendar(start_date, end_date, userId);
    });

    window.getWeekDataForCalendar = function (start_date, end_date, userId) {
        $.ajax({
            url: route('time-entries-calendar-list'),
            type: 'GET',
            dataType: 'json',
            data: {
                start_date: start_date,
                end_date: end_date,
                userId: userId,
            },
            success: function (result) {
                screenUnLock();
                if (result.success) {
                    let totalTime = 0;
                    $.each(result.data, function (key, value) {
                        totalTime += value.totalDuration;
                    });
                    $('#calendar').fullCalendar('removeEvents');
                    $('#calendar').fullCalendar('addEventSource', result.data);
                    $('#calendar').fullCalendar('rerenderEvents');
                    $('.totalTimeEntryHours').remove();
                    $('.fc-center').
                        append(
                            '<div class="totalTimeEntryHours badge badge-primary">' +
                            timeLogged + ' = ' +
                            roundToQuarterHourAllForCalendarView(totalTime) +
                            '</div>');
                }
            },
            error: function (error) {
                manageAjaxErrors(error);
            },
        });
    };

    window.onscroll = function () {
        $('.tooltip').hide();
    };
});
