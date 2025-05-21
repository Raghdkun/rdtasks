'use strict';

$(document).ready(function () {
    let userId = '';
    $('#user_id').select2({
        width: '100%',
    });

    window.onbeforeunload = function () {
        window.scrollTo(0, 0);
    };

    var count = 2;
    $(window).scroll(function () {
        if ($(window).scrollTop() == $(document).height() -
            $(window).height()) {
            loadArticle(count);
            count++;
        }
    });

    let dataCount = false;
    function loadArticle (count) {
        if (!dataCount) {
            $('.load-more-logs').show();
            $.ajax({
                url: route('activity-logs') + '?page=' + count,
                type: 'get',
                data: { user_id: userId },
                success: function (result) {
                    if (result.success) {
                        let activityLogsData = result.data.activityLogs.data;
                        let activityLogs = '';
                        let index;
                        if (activityLogsData.length > 0) {
                            dataCount = false;
                            for (index = 0; index <
                            activityLogsData.length; ++index) {
                                let data = [
                                    {
                                        'created_at': humanReadableFormatDate(
                                            activityLogsData[index].created_at),
                                        'subject_type': !isEmpty(
                                            result.data.resultData[index].modal)
                                            ? activityLogIconJS(
                                                result.data.resultData[index].modal)
                                            : activityLogIconJS('n/a'),
                                        'created_by': activityLogsData[index].created_by.name,
                                        'description': activityLogsData[index].description +
                                            ((result.data.resultData[index].data !=
                                                undefined)
                                                ? result.data.resultData[index].data
                                                : ''),
                                        'id': activityLogsData[index].id,
                                    }];
                                let activityLogDiv = prepareTemplateRender(
                                    '#activityLogsTemplate', data);
                                activityLogs += activityLogDiv;
                            }
                        } else {
                            dataCount = true;
                            $('.load-more-logs').hide();
                            $('.no-found-more-logs').
                                html(noMoreRecords);
                        }

                        $('.activities').append(activityLogs);
                    }
                },
                error: function (result) {
                    manageAjaxErrors(result);
                },
            });
        }
    }

    function humanReadableFormatDate (date) {
        return moment(date).fromNow();
    };

    function activityLogIconJS (model) {
        let className = model.substring(11);
        if (className == 'Department') {
            return 'fas fa-building';
        } else if (className == 'Client') {
            return 'fas fa-user-tie';
        } else if (className == 'Role') {
            return 'fas fa-user';
        } else if (className == 'Project') {
            return 'fas fa-folder-open';
        } else if (className == 'Task') {
            return 'fas fa-tasks';
        } else if (className == 'Report') {
            return 'fas fa-file';
        } else if (className == 'Invoice') {
            return 'fas fa-file-invoice';
        } else if (className == 'User') {
            return 'fas fa-users';
        } else if (className == 'Event') {
            return 'fas fa-calendar-day';
        } else {
            return 'fas fa-history';
        }
    };

    $(document).on('click', '.activityData', function () {
        let activityLogId = $(this).attr('data-id');
        deleteActivity(route('activity-delete',activityLogId), 'Activity Type',
            'Activity Log');
    });

    window.deleteActivity = function (url) {
        swal({
            title: deleteHeading + ' !',
            text: deleteMessage + ' "Activity Log" ?',
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#d33',
            cancelButtonText: noMessages,
            confirmButtonText: yesMessages,
        }, function () {
            $.ajax({
                url: url,
                type: 'DELETE',
                dataType: 'json',
                success: function success (obj) {
                    if (obj.success) {
                        window.livewire.emit('refresh');
                    }

                    swal({
                        title: 'Deleted!',
                        text: 'Activity Log has been deleted.',
                        type: 'success',
                        confirmButtonColor: '#6777EF',
                        timer: 2000,
                    })
                },
                error: function error (data) {
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
    };

    $(document).on('change', '#user_id', function () {
        userId = $(this).val();
        userFilterLivewire(userId);
        count = 2;
        dataCount = false;
    });

    window.userFilterLivewire = function ($userId) {
        window.livewire.emit('userFilter', $userId);
    };
});

document.addEventListener('livewire:load', function (event) {
    window.livewire.hook('message.processed', function () {
        $('#user_id').select2({
            width: '100%',
        });
    });
});
