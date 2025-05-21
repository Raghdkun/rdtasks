'use strict';
const departmentDropDown = $('#department');

$(document).ready(function () {
    $('#clientId').select2({
        width: '100%',
    });

    departmentDropDown.select2({
        width: '100%',
    });

    $('#projectIds').select2({
        width: '100%',
        placeholder: 'All Projects',
    });

    $('#userIds').select2({
        width: '100%',
        placeholder: 'All Users',
    });

    $('#tagIds').select2({
        width: '100%',
        placeholder: 'Select Tags',
    });

    $('#filterCreatedBy').select2();
});

$('.select2-search__field').css('width', '100%');

$('#start_date').datetimepicker({
    format: 'YYYY-MM-DD',
    useCurrent: true,
    locale: languageName == 'ar' ? 'en' : languageName,
    icons: {
        previous: 'icon-arrow-left icons',
        next: 'icon-arrow-right icons',
    },
    sideBySide: true,
    maxDate: moment(),
    widgetPositioning: {
        horizontal: 'left',
        vertical: 'bottom',
    },
});

$('#end_date').datetimepicker({
    format: 'YYYY-MM-DD',
    useCurrent: false,
    locale: languageName == 'ar' ? 'en' : languageName,
    icons: {
        previous: 'icon-arrow-left icons',
        next: 'icon-arrow-right icons',
    },
    sideBySide: true,
    maxDate: moment(),
    widgetPositioning: {
        horizontal: 'left',
        vertical: 'bottom',
    },
});

$('#start_date, #end_date').on('dp.show', function () {
    matchWindowScreenPixels({ startDate: '#start_date', endDate: '#end_date' },
        'rpt');
});

$(window).resize(function () {
    matchWindowScreenPixels({ startDate: '#start_date', endDate: '#end_date' },
        'rpt');
}).trigger('resize');

$(function () {
    $('form').find('input:text').filter(':input:visible:first').first().focus();
});

$('#start_date').on('dp.change', function (e) {
    $('#end_date').data('DateTimePicker').minDate(e.date);
});

$('#end_date').on('dp.change', function (e) {
    $('#start_date').data('DateTimePicker').maxDate(e.date);
});

departmentDropDown.on('change', function () {
    if (+$(this).val() !== 0) {
        $('#clientId').val(null).trigger('change');
    }
    loadClient(parseInt($(this).val()));
});
$(document).on('change', '#clientId', function () {
    $('#projectIds').empty();
    if ($(this).val() != 0) {
        $('#projectIds').val(null).trigger('change');
    }
    if ($(this).val() != '') {
        loadProjects($(this).val());
    }
});

function loadClient(departmentId) {
    let client;
    departmentId = (departmentId === 0) ? '' : departmentId;
    $('#clientId').val(null).trigger('change');
    $('#clientId').empty();
    $.ajax({
        url: route('clients-of-department') + '?department_id=' + departmentId,
        type: 'GET',
        success: function (result) {
            const clients = result.data;
            let options = '<option value="">All Clients</option>';
            $.each(clients, function (value, key) {
                if (client === undefined) {client = key;}
                options += '<option value="' + key + '">' + value + '</option>';
            });
            $('#clientId').html(options);
            $('#clientId').select2();
            if(client !== undefined) {
                $('#clientId').val(client).trigger('change');
            }
        },
    })
}

function loadProjects (clientId) {
    clientId = (clientId == 0) ? '' : clientId
    $.ajax({
        url: route('projects-of-client') + '?client_id=' + clientId,
        type: 'GET',
        success: function (result) {
            let element = document.createElement('textarea')
            const projects = result.data
            for (const key in projects) {
                element.innerHTML = projects[key]
                if (projects.hasOwnProperty(key)) {
                    $('#projectIds').
                        append(
                            $('<option>', { value: key, text: element.value }))
                }
            }
        },
    })
}

$('#projectIds').on('change', function () {
    $('#userIds').empty()
    $('#userIds').val(null).trigger('change')
    loadUsers($(this).val().toString())
});

function loadUsers (projectIds) {
    $.ajax({
        url: route('reports.users-of-projects') + '?projectIds=' + projectIds,
        type: 'GET',
        success: function (result) {
            const users = result.data
            for (const key in users) {
                if (users.hasOwnProperty(key)) {
                    $('#userIds').
                        append($('<option>', { value: key, text: users[key] }))
                }
            }
        },
    })
}

// open delete confirmation model
$(document).on('click', '.delete-btn', function (event) {
    let reportId = $(event.currentTarget).attr('data-id');
    deleteReport(route('reports.destroy',reportId));
});

window.deleteReport = function (url) {
    swal({
        title: deleteHeading + ' !',
        text: deleteMessage + ' "Report" ?',
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

                    if ($('#report_table').DataTable().data().count() == 1) {
                        $('#report_table').DataTable().page('previous').draw('page');
                    } else {
                        $('#report_table').DataTable().ajax.reload(null, false);
                    }
                }

                swal({
                    title: 'Deleted!',
                    text: 'Report has been deleted.',
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

let tbl = $('#report_table').DataTable({
    language: {
        'paginate': {
            'previous': '<i class="fas fa-angle-left"></i>',
            'next': '<i class="fas fa-angle-right"></i>',
        },
        'info': showingText + ' _START_ ' + toText + ' _END_ ' + ofText +
            ' _TOTAL_ ' + entriesText,
    },
    processing: true,
    serverSide: true,
    'order': [[0, 'asc']],
    ajax: {
        url: route('reports.index'),
        data: function (data) {
            data.filter_created_by = $('#filterCreatedBy').
                find('option:selected').
                val();
        },
    },
    columnDefs: [
        {
            'targets': [1, 2, 3],
            'width': '10%',
            'className': 'text-center',
        },
        {
            'targets': [4],
            'orderable': false,
            'className': 'text-center',
            'width': '80px',
        },
    ],
    columns: [
        {
            data: function (row){
                let element = document.createElement('textarea');
                element.innerHTML = row.name;
                return element.value;
            },
            name: 'name',
        },
        {
            data: function (row) {
                return format(row.start_date, 'YYYY-MMM-DD')
            },
            name: 'start_date',
        },
        {
            data: function (row) {
                return format(row.end_date, 'YYYY-MMM-DD')
            },
            name: 'end_date',
        },
        {

            data: function (row) {
                let element = document.createElement('textarea');
                element.innerHTML = !isEmpty(row.user) ? row.user.name : 'N/A';
                return element.value;
            },
            name: 'owner_id',
        },
        {
            data: function (row) {
                return actionTemplate({
                    Url: route('reports.index'),
                    viewText: viewText,
                    editText: editText,
                    deleteText: deleteText,
                    id: row.id,
                });
            }, name: 'id',
        },
    ],
    'fnInitComplete': function () {
        $(document).on('change', '#filterClient,#filterCreatedBy', function () {
            tbl.ajax.reload();
        });
    },
});

$(document).on('click', '.preview-btn', function () {
    const form = $(this).closest('form');
    const loadingButton = jQuery(this);
    loadingButton.button('loading');
    $('.enable_preview').prop('checked', true);
    let formdata = form.serializeArray();
    formdata = formdata.slice(1);
    $.ajax({
        url: route('reports.preview'),
        type: 'POST',
        data: formdata,
        success: function (result) {
            $('section.section').append(result.data);
            loadToggler();
        },
        error: function (result) {
            printErrorMessage('#validationErrorsBox', result);
        },
        complete: function () {
            loadingButton.button('reset');
        },
    })
});

$(document).on('click', '.save-btn', function () {
    const loadingButton = jQuery(this);
    loadingButton.button('loading');
    if($('#name').val().trim() == ''){
        displayErrorMessage('name field is required');
        loadingButton.button('reset');
        return false
    }
    if($('#start_date').val() == ''){
        displayErrorMessage('start date field is required');
        loadingButton.button('reset');
        return false
    }
    if($('#end_date').val() == ''){
        displayErrorMessage('end date field is required');
        loadingButton.button('reset');
        return false
    }
    $('.report-form').submit();
});

$(document).on('click', '.cancel-btn', function () {
    $(this).closest('.preview-wrapper').remove();
});

const loadToggler = () => {
    $(document).on('click', '.reports__department-row-title', function () {
        $(this).find('i.fa-caret-up').toggleClass('fa-rotate');
        $(this).parent().parent().find('.collapse-row').slideToggle();
    });
    $(document).on('click', '.reports__client-row-title', function () {
        $(this).find('i.fa-caret-up').toggleClass('fa-rotate');
        $(this).parent().next('.reports__client-container').slideToggle();
    });
    $(document).on('click', '.reports__project-header', function () {
        $(this).find('i.fa-caret-up').toggleClass('fa-rotate');
        $(this).parent().next('.reports__project-container').slideToggle();
    });
    $(document).on('click', '.reports__developer-header', function () {
        $(this).find('i.fa-caret-up').toggleClass('fa-rotate');
        $(this).parent().next('.reports__task-container').slideToggle();
    });
}
