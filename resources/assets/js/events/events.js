'use strict';
$(document).ready(function () {
    $('#AddModal,#EditModal').on('show.bs.modal', function () {
        $(this).appendTo('body');
    });

    $('#AddModal').on('hidden.bs.modal', function () {
        $('#description').summernote('code', '');
        $("#startDate").data("DateTimePicker").date(moment().format('YYYY-MM-DD')+' 00:00');
        $('#startDate').data('DateTimePicker').date(null)
        $("#endDate").data("DateTimePicker").date(moment().format('YYYY-MM-DD')+' 00:00');
        $('#endDate').data('DateTimePicker').date(null)
        resetModalForm('#addNewForm', '#validationErrorsBox');
        $('#type').val(1).trigger('change.select2');
    });

    $('#EditModal').on('hidden.bs.modal', function () {
        $('#editDescription').summernote('code', '');
        resetModalForm('#editForm', '#editValidationErrorsBox');
    });

    $('#description,#editDescription').summernote({
        placeholder: 'Add Event description...',
        minHeight: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['para', ['paragraph']]],
    });

    $('#type,#editType').select2({
        width: '100%',
    });
    $('#startDate, #endDate').datetimepicker({
        format: 'YYYY-MM-DD HH:mm',
        useCurrent: false,
        locale: languageName == 'ar' ? 'en' : languageName,
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'bottom',
        },
        icons: {
            previous: 'icon-arrow-left icons',
            next: 'icon-arrow-right icons',
            up: 'icon-arrow-up icons',
            down: 'icon-arrow-down icons',
            clear: 'icon-trash icons',
        },
        sideBySide: true,
        showClear: true,
    });
    $('#startDate').on('dp.change', function (e) {
        $('#endDate').data('DateTimePicker').minDate(e.date);
    });

    $('#editStartDate, #editEndDate').datetimepicker({
        format: 'YYYY-MM-DD HH:mm',
        useCurrent: false,
        locale: languageName == 'ar' ? 'en' : languageName,
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'bottom',
        },
        icons: {
            previous: 'icon-arrow-left icons',
            next: 'icon-arrow-right icons',
            up: 'icon-arrow-up icons',
            down: 'icon-arrow-down icons',
            clear: 'icon-trash icons',
        },
        sideBySide: true,
        showClear: true,
    });
    $('#editStartDate').on('dp.change', function (e) {
        $('#editEndDate').data('DateTimePicker').minDate(e.date);
    });

    $('#eventsCalendarFilter').datetimepicker(({
        format: 'MMMM/YYYY',
        viewMode: 'months',
        sideBySide: true,
        icons: {
            previous: 'icon-arrow-left icons',
            next: 'icon-arrow-right icons',
            up: 'icon-arrow-up icons',
            down: 'icon-arrow-down icons',
            clear: 'icon-trash icons',
        },
    }));

    let calendar = $('#calenders').fullCalendar({
        // themeSystem: 'bootstrap4',
        height: 850,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek',
        },
        nextDayThreshold: '00:00:00',
        forceEventDuration: true,
        defaultDate: new Date(),
        defaultView: 'month',
        editable: permission,
        droppable: true,
        allDaySlot: false,
        eventLimit: 3, // allow "more" link when too many events
        eventLimitText: 'More',
        locale:languageName,
        eventDrop: function (event) {
            if (permission) {
                eventsUpdateForCalendar(event);
            }
        },
        eventClick: function (event, el) {
            if (permission) {
                let value = el.target.classList.value;
                if (value == 'fa fa-trash' || value ==
                    'action-div justify-content-around') {
                    deleteEvent(event);
                } else {
                    editEvent(event);
                }
            }
        },
        eventRender: function (event, element) {
            element.find('.fc-time').empty();
            if (permission) {
                element.find('.fc-content').
                    append(
                        '<div class="action-div justify-content-around"><div style="padding-top: 5px;"><a href="#" class="delete-btn" data-id="' +
                        event.id +
                        '" title="' + eventDeleteText +
                        '"><i class="fa fa-trash"></i></a></div></div>');
            }
            if (event.type != 2) {
                element.find('.fc-title').
                    append('<br>' + event.start_time + ' to ' + event.end_time);
            }
        },
        eventAfterRender: function (event, element) {
            if (!permission) {
                element.find('.fc-content').css('cursor', 'default');
            }
            if (!isEmpty(event.description)) {
                $(element).tooltip({
                    title: event.description,
                    html: true,
                    container: 'body',
                });
            }
        },
    });

    let month = $('#eventsCalendarFilter').val();

    $(document).ready(function () {
        getEventsDataForCalendar(month);
    });

    $('.fc-today-button').click(function () {
        $('#eventsCalendarFilter').val(moment().format('MMMM/Y'));
        month = $('#eventsCalendarFilter').val();
    });

    $('.fc-agendaWeek-button').click(function () {
        if (month == moment().format('MMMM/Y')) {
            $('#calenders').
                fullCalendar('changeView', 'agendaWeek',
                    moment().format('YYYY-MM-DD'));
        }
    });

    $(document).on('dp.change', '#eventsCalendarFilter', function () {
        month = $(this).val();
        startLoader();
        getEventsDataForCalendar(month);
    });

    window.getEventsDataForCalendar = function (month) {
        $.ajax({
            url: route('events.data'),
            type: 'GET',
            success: function (result) {
                screenUnLock();
                if (result.success) {
                    $('#calenders').fullCalendar('removeEvents');
                    $('#calenders').
                        fullCalendar('changeView', 'month',
                            moment(month).format('YYYY-MM-DD'));
                    $('#calenders').fullCalendar('addEventSource', result.data);
                    $('#calendars').fullCalendar('rerenderEvents');
                }
            },
            error: function (error) {
                manageAjaxErrors(error);
            },
        });
    };

    window.eventsUpdateForCalendar = function (event) {
        let id = event.id;
        let start = moment(event.start).format('YYYY-MM-DD HH:mm:ss');
        let end = moment(event.end).format('YYYY-MM-DD HH:mm:ss');
        $.ajax({
            url: route('events.drop.update',id),
            type: 'POST',
            data: { start_date: start, end_date: end },
            success: function (result) {
                if (result.success) {
                    getEventsDataForCalendar();
                }
            },
            error: function (error) {
                manageAjaxErrors(error);
            },
        });
    };

    window.editEvent = function (event) {
        startLoader();
        $.ajax({
            url: route('events.edit',event.id),
            type: 'GET',
            success: function (result) {
                let events = result.data;
                if (result.success) {
                    $('#editTitle').val(events.title);
                    $('#editStartDate').
                        val(moment(events.start_date).
                            format('YYYY-MM-DD HH:mm'));
                    $('#editEndDate').
                        val(moment(events.end_date).format('YYYY-MM-DD HH:mm'));
                    $('#editEndDate').
                        data('DateTimePicker').
                        minDate(events.start_date);
                    $('#editType').val(events.type).trigger('change');
                    $('#editDescription').
                        summernote('code', events.description);
                    $('#Id').val(events.id);

                    $('#EditModal').modal('show');
                    stopLoader();
                }
            },
            error: function (error) {
                manageAjaxErrors(error);
            },
        });
    };

    $(document).on('submit', '#addNewForm', function (event) {
        event.preventDefault();
        let $description = $('<div />').
            html($('#description').summernote('code'));
        let empty = $description.text().trim().replace(/ \r\n\t/g, '') === '';
        let loadingButton = jQuery(this).find('#btnSave');
        loadingButton.button('loading');
        if ($('#description').summernote('isEmpty')) {
            $('#description').val('');
        } else if (empty) {
            displayErrorMessage(
                'Description field is not contain only white space');
            loadingButton.button('reset');
            return false;
        }
        let form = $(this);
        let formdata = $(this).serializeArray();

        $.ajax({
            url: route('events.index'),
            type: 'POST',
            data: formdata,
            success: function (result) {
                if (result.success) {
                    displaySuccessMessage(result.message);
                    $('#AddModal').modal('hide');
                    getEventsDataForCalendar(month);
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

    $(document).on('submit', '#editForm', function (event) {
        event.preventDefault();
        let id = $('#Id').val();
        let $description = $('<div />').
            html($('#editDescription').summernote('code'));
        let empty = $description.text().trim().replace(/ \r\n\t/g, '') === '';
        let loadingButton = jQuery(this).find('#btnSave');
        loadingButton.button('loading');
        if ($('#editDescription').summernote('isEmpty')) {
            $('#editDescription').val('');
        } else if (empty) {
            displayErrorMessage(
                'Description field is not contain only white space');
            loadingButton.button('reset');
            return false;
        }
        let form = $(this);
        let formdata = $(this).serializeArray();

        $.ajax({
            url: route('events.update',id),
            type: 'POST',
            data: formdata,
            success: function (result) {
                if (result.success) {
                    displaySuccessMessage(result.message);
                    $('#EditModal').modal('hide');
                    getEventsDataForCalendar(month);
                }
            },
            error: function (result) {
                printErrorMessage('#editValidationErrorsBox', result);
            },
            complete: function () {
                loadingButton.button('reset');
            },
        });
    });

    window.deleteEvent = function (event) {
        swal({
                title: deleteHeading + ' !',
                text: deleteMessage + ' "' + 'Event' + '" ?',
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
                $('#calenders').fullCalendar('editable', false);
                $.ajax({
                    url: route('events.delete',event.id),
                    type: 'DELETE',
                    dataType: 'json',
                    success: function (result) {
                        if (result.success) {
                            getEventsDataForCalendar(month);
                        }
                        swal({
                            title: 'Deleted!',
                            text: 'Event has been deleted.',
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
            },
        );
    };
});

window.onscroll = function () {
    $('.tooltip').hide();
};
