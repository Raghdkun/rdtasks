'use strict';
const jsrender = require('jsrender')
window.moment = require('moment')
moment.locale(languageName)

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
});
$(document).ajaxError(function (event, xhr, settings) {
    if (xhr.status == 401) {
        location.replace(route('login'));
    }
});

$(document).on('mouseenter', '.livewire-card', function () {
    $(this).find('.action-dropdown').removeClass('d-none');
});

$(document).on('mouseleave', '.livewire-card', function () {
    $(this).find('.action-dropdown').addClass('d-none');
    $(this).find('.action-dropdown').next().removeClass('show');
});

window.prepareTemplateRender = function (templateSelector, data) {
    let template = jsrender.templates(templateSelector);
    return template.render(data);
};

$.extend($.fn.dataTable.defaults, {
    'paging': true,
    'info': true,
    'ordering': true,
    'autoWidth': false,
    'pageLength': 10,
    'language': {
        'search': '',
        'sSearch': 'Search',
        'sProcessing': getSpinner(),
    },
    'preDrawCallback': function () {
        customSearch()
    },
})

function customSearch () {
    $('.dataTables_filter input').addClass('form-control');
    $('.dataTables_filter input').attr('placeholder', searchText);
}

function getSpinner () {
    return '<div id="infyLoader" class="infy-loader custom-js-spinner-css">\n' +
        '    <svg width="150px" height="75px" viewBox="0 0 187.3 93.7" preserveAspectRatio="xMidYMid meet"\n' +
        '         >\n' +
        '        <path stroke="#00c6ff" id="outline" fill="none" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"\n' +
        '              stroke-miterlimit="10"\n' +
        '              d="M93.9,46.4c9.3,9.5,13.8,17.9,23.5,17.9s17.5-7.8,17.5-17.5s-7.8-17.6-17.5-17.5c-9.7,0.1-13.3,7.2-22.1,17.1 \t\t\t\tc-8.9,8.8-15.7,17.9-25.4,17.9s-17.5-7.8-17.5-17.5s7.8-17.5,17.5-17.5S86.2,38.6,93.9,46.4z"/>\n' +
        '        <path id="outline-bg" opacity="0.05" fill="none" stroke="#f5981c" stroke-width="5" stroke-linecap="round"\n' +
        '              stroke-linejoin="round" stroke-miterlimit="10"\n' +
        '              d="\t\t\t\tM93.9,46.4c9.3,9.5,13.8,17.9,23.5,17.9s17.5-7.8,17.5-17.5s-7.8-17.6-17.5-17.5c-9.7,0.1-13.3,7.2-22.1,17.1 \t\t\t\tc-8.9,8.8-15.7,17.9-25.4,17.9s-17.5-7.8-17.5-17.5s7.8-17.5,17.5-17.5S86.2,38.6,93.9,46.4z"/>\n' +
        '    </svg>\n' +
        '</div>'
}

$(document).on('click', '.btn-task-delete', function (event) {
    let taskId = $(event.currentTarget).attr('data-task-id');
    deleteItem('tasks/' + taskId, '#task_table', 'Task')
    setTimeout(function () {
        revokerTracker()
    }, 1000)
})

function deleteItemAjax (url, tableId, header, callFunction = null) {
    $.ajax({
        url: url,
        type: 'DELETE',
        dataType: 'json',
        success: function (obj) {
            if (obj.success) {
                $(tableId).DataTable().ajax.reload(null, false);
                location.reload(true);
            }
            swal({
                title: 'Deleted!',
                text: header + ' has been deleted.',
                type: 'success',
                confirmButtonColor: '#6777EF',
                timer: 2000,
            })
            if (callFunction) {
                eval(callFunction)
            }
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
}

window.deleteItem = function (url, tableId, header, callFunction = null) {
    swal({
            title: deleteHeading + ' !',
            text: deleteMessage + ' "' + header + '" ?',
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
            deleteItemAjax(url, tableId, header, callFunction)
        })
}

window.deleteItemInputConfirmation = function (
    url, tableId, header, alertMessage, callFunction = null) {
    swal({
            type: 'input',
            inputPlaceholder: deleteConfirm + ' "' + deleteWord + '" ' +
                toTypeDelete + ' ' + header + '.',
            title: deleteHeading + ' !',
            text: alertMessage,
            html: true,
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#6777EF',
            cancelButtonColor: '#d33',
            cancelButtonText: noMessages,
            confirmButtonText: yesMessages,
            imageUrl: baseUrl + 'images/warning.png',
        },
        function (inputVal) {
            if (inputVal === false) {
                return false
            }
            if (inputVal == '' || inputVal.toLowerCase() != 'delete') {
                swal.showInputError(
                    'Please type "delete" to delete this  '+header+'.')
                $('.sa-input-error').css('top', '23px!important');
                $(document).find('.sweet-alert.show-input :input').val('');
                return false
            }
            if (inputVal.toLowerCase() === 'delete') {
                deleteItemAjax(url, tableId, header, callFunction = null)
            }
        })
}

window.printErrorMessage = function (selector, errorResult) {
    $(selector).show().html('')
    $(selector).text(errorResult.responseJSON.message)
    setTimeout(function () {
        $(selector).slideUp();
    }, 5000);
}

window.resetModalForm = function (formId, validationBox) {
    $(formId)[0].reset()
    $(validationBox).hide()
}

window.manageCheckbox = function (input) {
    if (input.id == 'enabled') {
        $(input).attr('name', 'no')
        $(input).iCheck({
            checkboxClass: 'icheckbox_line-white',
            insert: '<div class="icheck_line-icon"></div>',
        })
    } else {
        $(input).attr('name', 'yes')
        $(input).iCheck({
            checkboxClass: 'icheckbox_line-green',
            insert: '<div class="icheck_line-icon"></div>',
        })
    }
}
window.onload = function () {
    window.startLoader = function () {
        $('.infy-loader').show();
    };

    window.stopLoader = function () {
        $('.infy-loader').hide();
    };

// infy loader js
    stopLoader();
};

window.screenLock = function () {
    $('.infy-loader').show();
    $('body').css({'pointer-events': 'none', 'opacity': '0.6'});
};

window.screenUnLock = function () {
    $('body').css({'pointer-events': 'auto', 'opacity': '1'});
    $('.infy-loader').hide();
};

window.format = function (dateTime, format = 'DD-MMM-YYYY') {
    return moment(dateTime).format(format);
};

window.manageAjaxErrors = function (
    data, errorDivId = 'editValidationErrorsBox') {
    if (data.status == 404) {
        iziToast.error({
            title: 'Error!',
            message: data.responseJSON.message,
            position: 'topRight',
        });
    } else {
        printErrorMessage('#' + errorDivId, data)
    }
}
$(document).on('keydown', function (e) {
    if (e.keyCode === 27) {
        $('.modal').modal('hide')
    }
})
window.displaySuccessMessage = function (message) {
    iziToast.success({
        title: 'Success',
        message: message,
        position: 'topRight',
    });
}

$(document).ready(function (){

    let showgoogleCaptcha = $('#showRecaptcha').is(':checked');
    showgoogleCaptchaKey(showgoogleCaptcha)

    function showgoogleCaptchaKey(showgoogleCaptcha){
        if(showgoogleCaptcha){
            $(".google_captcha_key").show()
        }else{
            $(".google_captcha_key").hide()
        }
    }
    $("#showRecaptcha").on('click',function(){
        showgoogleCaptcha = $(this).is(':checked');
        showgoogleCaptchaKey(showgoogleCaptcha)
    });
})

$(function () {
    $('.dataTables_length').css('padding-top', '6px')
    $('.dataTables_info').css('padding-top', '24px')
})

$.extend($.fn.dataTable.defaults, {
    drawCallback: function (settings) {
        let thisTableId = settings.sTableId
        if (settings.fnRecordsDisplay() > settings._iDisplayLength) {
            $('#' + thisTableId + '_paginate').show()
        } else {
            $('#' + thisTableId + '_paginate').hide()
        }
    },
})

//focus on select2
$(document).
    on('focus', '.select2-selection.select2-selection--single', function (e) {
        $(this).
            closest('.select2-container').
            siblings('select:enabled').
            select2('open')
    })

$(function () {
    $('.modal').on('shown.bs.modal', function () {
        if ($(this).find('.timeEntryAddForm').hasClass('timeEntryAddForm') ||
            $(this).find('.editTimeEntryForm').hasClass('editTimeEntryForm')) {
            $(this).find('textarea').first().focus()
        } else {
            $(this).find('input:text').first().focus()
        }
    })
})

window.roundToQuarterHourAll = function (minuts) {
    var hours = Math.floor(minuts / 60);
    var minutes = minuts % 60;
    if (hours > 0) {
        return pad(hours) + ':' + pad(minutes) + ' h';
    } else {
        return pad(hours) + ':' + pad(minutes) + ' m';
    }
};

window.roundToQuarterHourAllForCalendarView = function (minuts) {
    let hours = Math.floor(minuts / 60);
    let minutes = minuts % 60;
    if (hours > 0) {
        return pad(hours) + ':' + pad(minutes) + ' Hours';
    } else {
        return pad(hours) + ':' + pad(minutes) + ' Minutes';
    }
};

window.pad = function (d) {
    return (d < 10) ? '0' + d : d;
};

window.nl2br = function (str, is_xhtml) {
    if (typeof str === 'undefined' || str === null) {
        return '';
    }
    let breakTag = (is_xhtml || typeof is_xhtml === 'undefined')
        ? '<br />'
        : '<br>';

    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g,
        '$1' + breakTag + '$2');
}

window.getItemFromLocalStorage = function (item) {
    return localStorage.getItem(item + '_' + loggedInUserId);
};

window.UnprocessableInputError = function (data) {
    iziToast.error({
        title: 'Error',
        message: data.responseJSON.message,
        position: 'topRight',
    });
};

var timeout = 3000; // in miliseconds (3*1000)
$('.alert').delay(timeout).fadeOut(300);

window.isEmpty = (value) => {
    return value === undefined || value === null || value === '';
};

window.displayErrorMessage = function (message) {
    iziToast.error({
        title: 'Error',
        message: message,
        position: 'topRight',
    });
};

$(document).ready(function () {
    // script to active parent menu if sub menu has currently active
    let hasActiveMenu = $(document).find('.nav-item.dropdown ul li').hasClass('active');
    if (hasActiveMenu) {
        $(document).find('.nav-item.dropdown ul li.active').parent('ul').css('display', 'block');
        $(document).find('.nav-item.dropdown ul li.active').parent('ul').parent('li').addClass('active');
    }
});

window.checkImageTag = function (rawHtml) {
    let imgExist = false;
    rawHtml.find('img').each(function () {
        imgExist = true;
    });
    if (imgExist) {
        displayErrorMessage('Image not allowed in Description');
        return false;
    }

    return true;
};

// matches screen pixels for media queries and applied the supplied css to the same
window.matchWindowScreenPixels = function (selectorObj, modulePrefix) {
    if (typeof selectorObj != 'undefined') {
        const windowWidth = $(window).innerWidth();
        if (windowWidth === 375) {
            $.each(selectorObj, function (key, val) {
                $(val + ' + .bootstrap-datetimepicker-widget.dropdown-menu').
                    addClass('dtPicker375-' + modulePrefix);
            });
        }
        if (windowWidth === 360) {
            $.each(selectorObj, function (key, val) {
                $(val + ' + .bootstrap-datetimepicker-widget.dropdown-menu').
                    addClass('dtPicker360-' + modulePrefix);
            });
        } else if (windowWidth === 320) {
            $.each(selectorObj, function (key, val) {
                $(val + ' + .bootstrap-datetimepicker-widget.dropdown-menu').
                    addClass('dtPicker320-' + modulePrefix);
            });
        }
    // }
};

//progress bar animation on my projects details screen
$('.myProgress').each(function () {

    let bar = $(this).find(".bar");
    let val = $(this).find("span");
    let percentage = parseInt( val.text(), 10);

    $({ p: 0 }).animate({ p: percentage }, {
        duration: 1000,
        easing: 'swing',
        step: function (p) {
            bar.css({
                transform: 'rotate(' + (45 + (p * 1.8)) + 'deg)', // 100%=180° so: ° = % * 1.8
                // 45 is to add the needed rotation to have the green borders at the bottom
            })
            val.text(p | 0)
        },
    });
});
};

window.actionTemplate = Handlebars.compile(
    '<a title="{{viewText}}" class="mt-1 mr-2 card-view-icon" href="{{Url}}/{{id}}"><i class="fas fa-eye card-view-icon"></i></a>' +
    '<a title="{{editText}}" class="edit-btn mr-2 mt-1 card-edit-icon" href="{{Url}}/{{id}}/edit">' +
    '<i class="fas fa-edit card-edit-icon"></i></a>' +
    '<a title="{{deleteText}}" class="delete-btn mt-1 card-edit-icon" href="javascript:void(0)" data-id="{{id}}">' +
    '<i class="fas fa-trash card-delete-icon"></i></a>');

let notifications = Handlebars.compile(
    '<a href="#" data-id="{{id}}" class="dropdown-item dropdown-item-unread readNotification"\n' +
    '                   id="readNotification">\n' +
    '                    <div class="dropdown-item-icon bg-primary text-white">\n' +
    '                        <i class="{{icon}}" style="line-height: unset;"></i>\n' +
    '                    </div>\n' +
    '                    <div class="dropdown-item-desc text-dark notification-title" style="width: 100%;">{{title}}\n' +
    '                        <div class="">\n' +
    '                            <span class="notification-for-text" style="line-break: anywhere;color: grey">{{description}}</span>\n' +
    '                        </div>\n' +
    '                        <div class="float-right">\n' +
    '                            <small class="notification-time">{{time}}</small>\n' +
    '                        </div>\n' +
    '                    </div>\n' +
    '                </a>');

$(document).ready(function () {
    getNotifications();
});
window.softDelete = Handlebars.compile(
    '<div class="d-flex justify-content-center w-100 h-100 mt-2"> <a title="{{deleteText}}" class="delete-btn mt-1 card-edit-icon soft-delete-btn" href="javascript:void(0)" data-id="{{id}}">' +
    '<i class="fas fa-trash card-delete-icon"></i></a></div>')


setInterval(function () {
    getNotifications();
}, 120000);

window.getNotifications = function () {
    $.ajax({
        url: '/get-notifications',
        method: 'GET',
        success: function (result) {
            if (result.success) {
                $('.notification-content').find('a').remove();
                if (result.data.length > 0) {
                    $('.notification-content').css('overflow-y', 'auto');
                    $('.nav-link.notification-toggle').addClass('beep');
                    $('#allRead').removeClass('d-none');
                    $('.empty-notification').addClass('d-none');
                    $.each(result.data, function (el, val) {
                        $('.notification-content').append(notifications({
                            id: val.id,
                            title: val.title,
                            description: val.description,
                            icon: HeaderNotificationIconJS(val.type),
                            time: moment(val.created_at).fromNow(),
                        }));
                    });
                } else {
                    $('#allRead').addClass('d-none');
                    $('.empty-notification').removeClass('d-none');
                }
            }
        },
    });
};

$(document).on('click', '#readNotification', function (e) {
    e.preventDefault();
    e.stopPropagation();
    let notificationId = $(this).data('id');
    let notification = $(this);
    notification.remove();
    $.ajax({
        type: 'POST',
        url: '/notification/' + notificationId + '/read',
        data: { notificationId: notificationId },
        success: function () {
            let notificationCounter = document.getElementsByClassName(
                'readNotification').length;
            if (notificationCounter == 0) {
                $('#allRead').addClass('d-none');
                $('.empty-notification').removeClass('d-none');
                $('.nav-link.notification-toggle').removeClass('beep');
            }
        },
        error: function (error) {
            manageAjaxErrors(error);
        },
    });
});

$(document).on('click', '#allRead', function (e) {
    e.preventDefault();
    e.stopPropagation();
    $.ajax({
        type: 'POST',
        url: '/read-all-notification',
        success: function () {
            $('.readNotification').remove();
            $('#allRead').addClass('d-none');
            $('.empty-notification').removeClass('d-none');
            $('.nav-link.notification-toggle').removeClass('beep');
        },
        error: function (error) {
            manageAjaxErrors(error);
        },
    });
});

function HeaderNotificationIconJS (model) {
    let className = model.substring(11);
    if (className == 'Project') {
        return 'fas fa-folder-open';
    } else if (className == 'Task') {
        return 'fas fa-tasks';
    } else if (className == 'Invoice') {
        return 'fas fa-file-invoice';
    } else if (className == 'User') {
        return 'fas fa-users';
    } else {
        return 'fas fa-bell';
    }
}

window.wc_hex_is_light = function(color) {
    const hex = color.replace('#', '');
    const c_r = parseInt(hex.substr(0, 2), 16);
    const c_g = parseInt(hex.substr(2, 2), 16);
    const c_b = parseInt(hex.substr(4, 2), 16);
    const brightness = ((c_r * 299) + (c_g * 587) + (c_b * 114)) / 1000;
    return brightness > 240;
}
