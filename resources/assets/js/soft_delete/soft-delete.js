'use strict';
$(document).ready(function (){
    let tbl = $('#deleteUser').DataTable({
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
            url: route('archived-users'),
        },
        columnDefs: [
            {
                'targets': [1],
                'orderable': false,
                'className': 'text-center',
                'width': '80px',
            },  
        ],
        columns: [
            {
                data: function (row) {
                    return '<div class="d-flex align-items-center" style="width: 50px;height: 50px">' +
                        '<img src="' + row.img_avatar +
                        '" class="rounded-circle thumbnail-rounded table-image" style="width: 50px;height: 50px"' +
                        '/>' +
                        '<div class="ml-3">' +
                        '<span class="d-block">' + row.name + '</span>' +
                        '<span>' + row.email + '</span>' +
                        '</div>' +
                        '</div>' +

                        ''
                },
                name: 'name',
              
            },
            {
                data: function (row) {
                    return softDelete({
                        deleteText: deleteText,
                        id: row.id,
                    });
                },
                name: 'id',
            },
            
        ],
        'preDrawCallback': function () {
            $('.dataTables_filter input').addClass('form-control');
            $('.dataTables_filter input').attr('placeholder', searchText);
        },
    })
})
$(document).on('click', '.delete-btn', function (event) {
    let reportId = $(event.currentTarget).attr('data-id');

    deleteReport(route('destroy-user',reportId));
});
window.deleteReport = function (url) {
    swal({
        title: deleteHeading + ' !',
        text: deleteMessage + ' "User" ?',
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

                    if ($('#deleteUser').DataTable().data().count() == 1) {
                        $('#deleteUser').DataTable().page('previous').draw('page');
                    } else {
                        $('#deleteUser').DataTable().ajax.reload(null, false);
                    }
                }

                swal({
                    title: 'Deleted!',
                    text: 'User has been deleted.',
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
