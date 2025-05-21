$(document).ready(function () {
    'use strict';

    $('#clientSelectBox,#discountTypeSelect').select2({
        width: '100%',
    });

    $('.projects-select-box').select2({
        width: '100%',
    });

    $('.task-select-box').select2({
        width: '100%',
    });

    $('.tax-select-box').select2({
        width: canManageTax?'calc(100% - 44px)':'100%',
    });

    $('#filter_status,#due_date_filter').select2();

    $('.issue-datepicker').datetimepicker({
        format: 'YYYY-MM-DD',
        useCurrent: false,
        locale: languageName == 'ar' ? 'en' : languageName,
        icons: {
            previous: 'icon-arrow-left icons',
            next: 'icon-arrow-right icons',
        },
        sideBySide: true,
        maxDate: new Date(),
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'bottom',
        },
    });

    $('.due-datepicker').datetimepicker({
        format: 'YYYY-MM-DD',
        useCurrent: false,
        locale: languageName == 'ar' ? 'en' : languageName,
        icons: {
            previous: 'icon-arrow-left icons',
            next: 'icon-arrow-right icons',
        },
        sideBySide: true,
        minDate: moment().subtract(1, 'days'),
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'bottom',
        },
    });

    $('.issue-datepicker, .due-datepicker').on('dp.show', function () {
        matchWindowScreenPixels(
            { issueDate: '.issue-datepicker', dueDate: '.due-datepicker' },
            'inv');
    });

    $(window).resize(function () {
        matchWindowScreenPixels(
            { issueDate: '.issue-datepicker', dueDate: '.due-datepicker' },
            'inv');
    }).trigger('resize');

    $('#invoiceNumber').attr('readonly', true);
    $('#clientSelectBox,.projects-select-box').prop('disabled', true);

    let tax = 0;
    let subTotal = 0;
    let discount = 0;
    let discountType;
    let fixRateAmount = $('#fixRateAmount').val();
    if (invoiceEdit) {
        let invoiceNetTotal = $('#netTotal').text();
        let invoiceSubTotal = $('#subTotal').text();
        calculateSubTotal();
        $('#netTotal').text(currency(invoiceNetTotal).format());
        $('#subTotal').text(currency(invoiceSubTotal).format());
    }
    if (isCreate) {
        calculateSubTotal();
        $('#subTotal').text(currency(subTotal).format());
        $('#netTotal').text(currency(subTotal).format());
    }

    $(document).on('change', '#clientSelectBox', function () {
        $('.projects-select-box').children('.new-option').remove();
        $('.projects-select-box').val('').trigger('change');
        let clientId = $(this).val();
        if (clientId !== '') {
            $.ajax({
                url: route('get.client-projects',clientId),
                type: 'GET',
                success: function (result) {
                    $.each(result.data, function (index, value) {
                        let data = [
                            {
                                'value': index,
                                'label': value,
                            }];
                        const projectOptionHtml = prepareTemplateRender(
                            '#optionsTemplate', data);
                        $('.projects-select-box').append(projectOptionHtml);
                        $('.projects-select-box').select2({
                            width: '100%',
                        });
                    });
                }
            });
        }
    });

    $(document).on('change', '.projects-select-box', function () {
        $('.task-select-box').children('.new-option').remove();
        $('.task-select-box').val('').trigger('change');
        let projectId = $(this).val();
        $.ajax({
            url: route('get.project-tasks',projectId),
            type: 'get',
            success: function (result) {
                $.each(result.data, function (index, value) {
                    let data = [
                        {
                            'value': index,
                            'label': value,
                        }];
                    const taskOptionHtml = prepareTemplateRender(
                        '#optionsTemplate', data);
                    $('.task-select-box').append(taskOptionHtml);
                    $('.task-select-box').select2({
                        width: '100%',
                    });
                });
            }
        });
    });

    if (invoiceEdit) {
        discountType = $('#discountTypeSelect').val();
        readOnlyDiscount(discountType);
    }

    function readOnlyDiscount (discountType) {
        if (discountType == 0) {
            discountType = $('#discount').val('0');
            $('#discount').prop('readonly', true);
        }
    }

    $(document).on('change', '#discountTypeSelect', function () {
        $('#discount').prop('readonly', false);
        discountType = $('#discountTypeSelect').val();
        readOnlyDiscount(discountType);
        calculateTaxAmount($('#subTotal').text());
        calculateNetTotal();
    });

    $(document).on('change', '.tax-select-box', function () {
        tax = 0;
        if ($(this).val() !== '') {
            tax = taxRatesArr[$(this).val()];
        }
        $('#invoiceTax').text(tax);
        calculateTaxAmount($('#subTotal').text());
        calculateNetTotal();
    });

    $(document).on('change', '.task-select-box', function () {
        let taskId = $(this).val();
        if (taskId !== '') {
            $.ajax({
                url: route('get.task-details',taskId),
                type: 'get',
                success: function (result) {
                    let invoiceItem = $('.items-container').
                        find('tr:last-child');
                    if (invoiceItem.find('.item-name').val() !== '') {
                        $('#itemAddBtn').trigger('click');
                        invoiceItem = $('.items-container').
                            find('tr:last-child');
                    }
                    invoiceItem.find('.task-id').val(result.data.id);
                    invoiceItem.find('.item-name').val(result.data.title);
                    invoiceItem.find('.hours').val(result.data.duration);
                    if (!isEmpty(result.data.description)) {
                        invoiceItem.find('.item-description').
                            val(result.data.description);
                    } else {
                        invoiceItem.find('.item-description').
                            val('N/A');
                    }
                },
            });
        }
    });

    $(document).on('click', '#itemAddBtn', function (e) {
        e.preventDefault();
        let data = [
            {
                'currencyClass': currentCurrency,
            }];
        const invoiceItemHtml = prepareTemplateRender(
            '#invoiceItemTemplate', data);
        $('.items-container').append(invoiceItemHtml);
    });

    $(document).on('click', '.remove-invoice-item', function (e) {
        e.preventDefault();
        $(this).parent().parent().remove();
        calculateSubTotal();
        calculateTaxAmount($('#subTotal').text());
        calculateNetTotal();
    });

    $(document).on('keyup', '.hours', function () {
        let regex = /^\d{0,4}(\.\d{0,2})?$/;
        if (!regex.test($(this).val())) {
            $(this).val(0);
        }
        calculateSubTotal();
        calculateTaxAmount($('#subTotal').text());
        calculateNetTotal();
    });

    $(document).on('keyup', '.task-amount', function () {
        $(this).
            val($(this).
                val().
                replace(/[^0-9.]/g, '').
                replace(/(\..*)\./g, '$1'));
        let taskAmount = removeCommas($(this).val());
        if ($(this).val() != '') {
            $(this).val(taskAmount);
        } else {
            $(this).val(0);
        }
        calculateSubTotal();
        calculateTaxAmount($('#subTotal').text());
        calculateNetTotal();

    });

    function calculateSubTotal () {
        subTotal = 0;
        $('.items-container>tr').each(function () {
            let taskItemAmount = $(this).find('.task-amount').val();
            subTotal += parseFloat(removeCommas(taskItemAmount));
            subTotal = parseFloat(subTotal);
        });
        subTotal = subTotal + parseFloat(fixRateAmount);
        $('#subTotal').text(getFormattedPrice(subTotal));
    };

    $(document).on('keyup', '#discount', function () {
        if (isEmpty(discountType)) {
            $(this).val('');
            displayErrorMessage('Select Discount Apply.');
            return false;
        }
        discount = $(this).
            val().
            replace(/,/g, '').
            replace(/[^0-9.]/g, '').
            replace(/(\..*)\./g, '$1');
        $(this).val(currency(discount, { precision: 0 }).format());
        calculateTaxAmount($('#subTotal').text());
        calculateNetTotal();
        if (parseFloat($('#netTotal').text()) < 0) {
            displayErrorMessage('Total amount should be greater than 0.');
            return false;
        }
    });

    function calculateTaxAmount(subTotalAmount) {
        let taxAmount = 0;
        if (discountType == 1) {
            let beforeTaxTotal = parseFloat(subTotal) - discount;
            taxAmount = (beforeTaxTotal * tax) / 100;
        } else {
            taxAmount = (removeCommas(subTotalAmount) * tax) / 100;
        }
        $('#taxAmount').text(currency(taxAmount).format());
    }

    const calculateNetTotal = () => {
        if (discountType == 1) {
            let beforeTaxTotal = parseFloat(subTotal) - discount;
            let taxAmount = (beforeTaxTotal * tax) / 100;
            let netTotal = (beforeTaxTotal + parseFloat(taxAmount)).toFixed(2);
            $(document).find('#netTotal').text(currency(netTotal).format());
        } else if (discountType == 2) {
            let taxAmount = (subTotal * tax) / 100;
            let AfterTaxTotal = (parseFloat(subTotal) +
                parseFloat(taxAmount)).toFixed(2);
            let netTotal = AfterTaxTotal - discount;
            $(document).find('#netTotal').text(currency(netTotal).format());
        } else {
            let taxAmount = (subTotal * tax) / 100;
            discount = 0;
            let netTotal = (parseFloat(subTotal) +
                parseFloat(taxAmount)).toFixed(2) - discount;
            $(document).find('#netTotal').text(currency(netTotal).format());
        }
    }

    $(document).on('click', '#saveAsDraft, #saveAndSend', function (e) {
        if (parseFloat($('#netTotal').text()) < 0) {
            displayErrorMessage('Total amount should be greater than 0.');
            return false;
        }
        e.preventDefault();
        screenLock();
        let invoiceStatus = $(this).data('status');

        let myForm = document.getElementById('invoiceForm');
        $('#invoiceForm').validate({
            errorPlacement: function (error, element) {
                //element.after(error);
            },
        }).settings.ignore = ':disabled,:hidden';

        if (!$('#invoiceForm').valid()) {
            screenUnLock();
            return false;
        }
        $('#clientSelectBox,.projects-select-box').prop('disabled', false);
        let formData = new FormData(myForm);
        formData.append('invoice_status', invoiceStatus);
        formData.append('report_id', $('#reportId').val());
        let index = 0;
        let item, taskId, desc, hours, rate, taskAmount, totalAmount, totalHour,
            projectId, fixRate;
        totalAmount = $('#netTotal').text();
        totalHour = $('#totalHour').text();
        $('.items-container>tr').each(function () {
            item = $(this).find('.item-name').val();
            taskId = '';
            if ($(this).find('.task-id').val() != undefined) {
                taskId = $(this).find('.task-id').val();
            }
            if ($(this).find('.item_project_id').val() != undefined) {
                projectId = $(this).find('.item_project_id').val();
            }
            if ($(this).find('.fix_rate').val() != undefined) {
                fixRate = $(this).find('.fix_rate').val();
            }
            hours = $(this).find('.hours').val();
            taskAmount = $(this).find('.task-amount').val();

            formData.append('itemsArr[' + index + '][item_name]', item);
            formData.append('itemsArr[' + index + '][task_id]', taskId);
            formData.append('itemsArr[' + index + '][item_project_id]',
                projectId);
            formData.append('itemsArr[' + index + '][fix_rate]', fixRate);
            formData.append('itemsArr[' + index + '][hours]', hours);
            formData.append('itemsArr[' + index + '][task_amount]', taskAmount);
            index++;
        });

        if (isEmpty(item) || isEmpty(hours) || hours == '0') {
            displayErrorMessage('Please fill all the required fields.');
            $('#clientSelectBox,.projects-select-box').prop('disabled', true);
            screenUnLock();
            return false;
        }

        formData.append('amount', totalAmount);
        formData.append('sub_total', subTotal);
        formData.append('total_hour', totalHour);
        $.ajax({
            url: route('invoices.store'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (result) {
                if (result.success) {
                    window.location.href = invoicesUrl + '/' + result.data.id;
                }
            },
            error: function (result) {
                screenUnLock();
                $('#clientSelectBox,.projects-select-box').
                    prop('disabled', true);
                manageAjaxErrors(result, 'validationErrorsBox');
            },
        });

    });
    $(document).on('click', '#editSaveAsDraft, #editSaveAndSend', function (e) {
        if ((parseFloat($('#netTotal').text())) < 0) {
            displayErrorMessage('Total amount should be greater than 0.');
            return false;
        }
        e.preventDefault();
        screenLock();
        let invoiceStatus = $(this).data('status');

        let myForm = document.getElementById('editInvoiceForm');
        $('#editInvoiceForm').validate({
            errorPlacement: function (error, element) {
                //element.after(error);
            },
        }).settings.ignore = ':disabled,:hidden';

        if (!$('#editInvoiceForm').valid()) {
            screenUnLock();
            return false;
        }
        $('#clientSelectBox,.projects-select-box').prop('disabled', false);
        let invoiceId = $('#hdnInvoiceId').val();
        let formData = new FormData(myForm);

        formData.append('invoice_status', invoiceStatus);

        let index = 0;
        let item, taskId, desc, hours, rate, taskAmount, totalAmount, totalHour,
            projectId, fixRate;
        totalAmount = $('#netTotal').text();
        totalHour = $('#totalHour').text();
        $('.items-container>tr').each(function () {
            item = $(this).find('.item-name').val();
            taskId = '';
            if ($(this).find('.task-id').val() != undefined) {
                taskId = $(this).find('.task-id').val();
            }
            if ($(this).find('.item_project_id').val() != undefined) {
                projectId = $(this).find('.item_project_id').val();
            }
            if ($(this).find('.fix_rate').val() != undefined) {
                fixRate = $(this).find('.fix_rate').val();
            }
            hours = $(this).find('.hours').val();
            taskAmount = $(this).find('.task-amount').val();

            formData.append('itemsArr[' + index + '][item_name]', item);
            formData.append('itemsArr[' + index + '][task_id]', taskId);
            formData.append('itemsArr[' + index + '][item_project_id]',
                projectId);
            formData.append('itemsArr[' + index + '][fix_rate]', fixRate);
            formData.append('itemsArr[' + index + '][hours]', hours);
            formData.append('itemsArr[' + index + '][task_amount]', taskAmount);
            index++;
        });

        if (isEmpty(item) || isEmpty(hours) || hours == '0') {
            displayErrorMessage('Please fill all the required fields.');
            $('#clientSelectBox,.projects-select-box').prop('disabled', true);
            screenUnLock();
            return false;
        }

        formData.append('amount', totalAmount);
        formData.append('sub_total', subTotal);
        formData.append('total_hour', totalHour);

        var loadingButton = jQuery(this);
        loadingButton.button('loading');

        $.ajax({
            url: invoicesUrl + '/' + invoiceId,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (result) {
                if (result.success) {
                    if(!userIsClient){
                        window.location.href = invoicesUrl + '/' + result.data.id;
                    }
                }
            },
            error: function (result) {
                screenUnLock();
                $('#clientSelectBox,.projects-select-box').
                    prop('disabled', true);
                manageAjaxErrors(result, 'validationErrorsBox');
            },
            complete: function () {
                loadingButton.button('reset');
            },
        });

    });

    if (typeof invoiceEdit !== 'undefined' && invoiceEdit) {
        $('.projects-select-box, .tax-select-box').trigger('change');
        $('#discount').trigger('keyup');
    }

    $(document).on('click', '.delete-btn', function (event) {
        let invoiceId = $(event.currentTarget).attr('data-id');
        deleteItem(route('invoices.destroy',invoiceId), 'tableName', 'Invoice',
            'location.reload()');
    });

    $(document).on('change', '#filter_status', function () {
        window.livewire.emit('filterTasksByStatus', $(this).val());
    });

    $(document).on('change', '#due_date_filter', function () {
        window.livewire.emit('filterDueDate', $(this).val());
    });

    $(document).on('submit', '#addNewForm', function (e) {
        e.preventDefault();
        var loadingButton = jQuery(this).find('#btnSave');
        loadingButton.button('loading');
        $.ajax({
            url: '/taxes',
            type: 'POST',
            data: $(this).serialize(),
            success: function (result) {
                $("#taxId, #editTaxId").empty();
                if (result.success) {
                    displaySuccessMessage(result.message);
                    var option = "<option value=''>Select Tax</option>";
                    $.each(result.data.taxes, function(key, value) {
                        option += "<option value='"+value+"'>"+key+"</option>";
                        $("#taxId, #editTaxId").html(option);
                    });
                    window.taxRatesArr = []
                    taxRatesArr = result.data.taxesArr;
                    $('#taxId').val(result.data.tax.id).trigger('change');
                    $('#editTaxId').val(result.data.tax.id).trigger('change');
                    $('#AddModal').modal('hide');
                }
            },
            error: function (result) {
                printErrorMessage('#taxValidationErrorsBox', result);
            },
            complete: function () {
                loadingButton.button('reset');
            },
        });
    });


    $('#AddModal').on('hidden.bs.modal', function () {
        resetModalForm('#addNewForm', '#taxValidationErrorsBox');
    });

});

