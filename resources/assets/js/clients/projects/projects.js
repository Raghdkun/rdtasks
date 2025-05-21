$(document).ready(function () {
    'use strict';

    $('#projectStatus').select2({
        width: '100%',
    });

    $(document).on('change', '#projectStatus', function () {
        let projectStatus = $(this).val();
        projectStatusLivewire(projectStatus);
    });

    window.projectStatusLivewire = function ($projectStatus) {
        window.livewire.emit('projectsStatus', $projectStatus);
    };

});
document.addEventListener('livewire:load', function () {
    window.livewire.hook('message.processed', () => {
        $('#projectStatus').select2({
            width: '100%',
        })
    })
})

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
