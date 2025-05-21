'use strict';
$(document).ready(function (){

    let tbl = $('#expense_table').DataTable({
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
            url: route('expenses.index'),
            },
            columnDefs: [
                {
                    'targets': [5],
                    'orderable': false,
                    'className': 'text-center',
                    'width': '80px',
                },
            ],
            columns: [
                {
                    data: function (row) {
                        return format(row.date, 'DD-MM-YYYY')
                    },
                    name: 'date',
                },
                {
                    data: function (row){
                        console.log(row.project.currency)
                        return '<i class="'+ getCurrency(row.project.currency) +'"></i> ' + getFormattedPrice(row.amount);
                    },
                    name: 'amount',
                },
                {
                    data: function (row){
                        if(row.user != null){
                            let element = document.createElement('textarea');
                            element.innerHTML = row.user.name;
                            return  element.value;
                        }else{
                            return 'N/A';
                        }
                    },
                    name: 'user.name',
                },
                {
                    data: function (row){
                        let element = document.createElement('textarea');
                        element.innerHTML = row.client.name;
                        return element.value;
                    },
                    name: 'client.name',
                },
                {
                    data: function (row){
                        let element = document.createElement('textarea');
                        element.innerHTML = row.project.name;
                        return element.value;
                    },
                    name: 'project.name',
                },
                {
                    data: function (row) {
                        return actionTemplate({
                            Url: route('expenses.index'),
                            viewText: viewText,
                            editText: editText,
                            deleteText: deleteText,
                            id: row.id,
                        });
                    }, name: 'id',
                },
            ],
        'preDrawCallback': function () {
            $('.dataTables_filter input').addClass('form-control');
            $('.dataTables_filter input').attr('placeholder', searchText);
        },
    });

    $('#clientId').select2({
        width: '100%',
        placeholder : 'Select Client'
    });

    $('#projectId').select2({
        width: '100%',
        placeholder : 'Select Project'
    });

    $('#category').select2({
        width: '100%',
    });

    $('#expenseDescription').summernote({
        placeholder: 'Add Expense description...',
        minHeight: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['para', ['paragraph']]],
    });

    $('#date').datetimepicker({
        format: 'YYYY-MM-DD',
        useCurrent: false,
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


    $(document).on('change','#clientId',function (){
        $('#projectId').empty();
        $('#projectId').val(null).trigger('change');
       let clientId = $(this).val();
       $.ajax({
           url: route('expenses.project',clientId),
           type: 'GET',
           success : function (result){
               let element = document.createElement('textarea');
               const projects = result.data;
               if(projects == ''){
                   displayErrorMessage('This client don\'t have projects.');
                   return false;
               }
               for (const key in projects) {
                   element.innerHTML = projects[key];
                   if (projects.hasOwnProperty(key)) {
                       $('#projectId').append('<option></option>').trigger('change');
                       $('#projectId').
                           append($('<option>', { value: key, text: element.value }));
                   }
               }
           }
       });
    });

    $(document).on('click', '.delete-btn', function (event) {
        let expenseId = $(event.currentTarget).attr('data-id');
        deleteExpense(route('expenses.destroy',expenseId));
    });

    window.deleteExpense = function (url) {
        swal({
            title: deleteHeading + ' !',
            text: deleteMessage + ' "Expense" ?',
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#6777EF',
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

                        if ($('#expense_table').DataTable().data().count() == 1) {
                            $('#expense_table').DataTable().page('previous').draw('page');
                        } else {
                            $('#expense_table').DataTable().ajax.reload(null, false);
                        }
                    }

                    swal({
                        title: 'Deleted!',
                        text: 'Expense has been deleted.',
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

    $(document).on('submit','.expense-form', function(){
        $('.files-count').empty();
        let $description = $('<div />').html($('#expenseDescription').summernote('code'));
        let empty = $description.text().trim().replace(/ \r\n\t/g, '') === '';
        let loadingButton = jQuery(this).find('.save-btn');
        loadingButton.button('loading');
        if ($('#amount').val() == 0) {
            displayErrorMessage('The amount should be a minimum of 1.');
            loadingButton.button('reset');
            return false;
        }
        if($('#expenseDescription').summernote('isEmpty')){
            $('#expenseDescription').val('');
        } else if (empty){
            displayErrorMessage('Description field is not contain only white space');
            loadingButton.button('reset');
            return false;
        }
    });

    if($('#attachment').length){
        document.querySelector('#attachment').addEventListener('change', handleFileSelect, false);
        let selDiv = document.querySelector('#attachmentPicturePreview');

        function handleFileSelect (e) {
            $('.files-count').empty();
            if (!e.target.files || !window.FileReader) return;

            selDiv.innerHTML = '';
            let files = e.target.files;
            for (let i = 0; i < files.length; i++) {
                let f = files[i];
                let reader = new FileReader();
                let ext = f.name.split('.').pop().toLowerCase();
                if ($.inArray(ext, ['png', 'jpg', 'jpeg', 'xls', 'xlsx', 'csv', 'pdf', 'doc', 'docx']) == -1) {
                    displayErrorMessage('The attachment must be a file of type: jpeg, jpg, png, xls, xlsx, pdf, doc');
                    $(this).val('');
                    $('#attachmentPicturePreview').addClass('d-none');
                    return false;
                }else{
                    $('#attachmentPicturePreview').removeClass('d-none');
                    reader.onload = function (e) {
                        if (f.type.match('image*')) {
                            let html = '<img class=\'img-thumbnail expense-thumbnail-preview mr-3 mb-2\' src="' +
                                e.target.result + '">';
                            selDiv.innerHTML += html;
                        } else if (f.type.match('pdf*')) {
                            let html = '<img class=\'img-thumbnail expense-thumbnail-preview mr-3 mb-2\' src="/assets/img/pdf_icon.png">';
                            selDiv.innerHTML += html;
                        }else if (f.type.match('sheet*') || f.type.match('ms-excel*')) {
                            let html = '<img class=\'img-thumbnail expense-thumbnail-preview mr-3 mb-2\' src="/assets/img/xls_icon.png">';
                            selDiv.innerHTML += html;
                        } else if (f.type.match('msword*')) {
                            let html = '<img class=\'img-thumbnail expense-thumbnail-preview mr-3 mb-2\' src="/assets/img/doc_icon.png">';
                            selDiv.innerHTML += html;
                        } else {
                            selDiv.innerHTML += f.name;
                        }

                    };
                    reader.readAsDataURL(f);
                }
            }
            let totalFiles = files.length;
            let message = (totalFiles == 1) ? ' file selected' : ' files selected';
            $('.files-count').append(totalFiles + message);
        }
    }

    $(document).on('click','.delete-attachment', function(){
        let Id = $(this).attr('data-id');
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
                    url : route('expense.delete-attachment',Id),
                    data: { filename: name ,id : Id},
                    success: function () {
                        swal({
                            title: 'Deleted!',
                            text: 'Attachment has been deleted.',
                            type: 'success',
                            confirmButtonColor: '#6777EF',
                            timer: 2000
                        });
                        location.reload();
                    },
                    error: function (e) {
                        manageAjaxErrors();
                    },
                });
            });
    });

    function getCurrency(currency) {
        switch (currency) {
            case 1:
                return 'fas fa-rupee-sign';
                break;
            case 2:
                return 'fas fa-dollar-sign';
                break;
            case 3:
                return 'fas fa-dollar-sign';
                break;
            case 4:
                return 'fas fa-dollar-sign';
                break;
            case 5:
                return 'fas fa-euro-sign';
                break;
            case 6:
                return 'fas fa-pound-sign';
                break;
            default:
                return 'fas fa-dollar-sign';
        }
    }
});
