'use strict';

$(window).on('load', function () {
    loadClientInvoices();
});

window.loadClientInvoices = function () {
    $.ajax({
        type: 'GET',
        url: clientInvoiceUrl,
        cache: false,
    }).done(prepareClientInvoiceChart);
};

window.prepareClientInvoiceChart = function (result) {
    $('#client-invoices-container').html('');
    let data = result.data;
    if (data.dataPoints.every(item => item === 0)) {
        $('#client-invoices-container').empty();
        $('#client-invoices-container').append(
            '<div align="center" class="no-record">'+ noRecordFoundMessage +'</div>');
        return true;
    } else {
        $('#client-invoices-container').html('');
        $('#client-invoices-container').
            append('<canvas id="client-invoices"></canvas>');
    }
    let ctx = document.getElementById('client-invoices').getContext('2d');
    ctx.canvas.style.height = '350px';
    ctx.canvas.style.width = '100%';
    let pieChartData = {
        labels: data.labels,
        datasets: [
            {
                data: data.dataPoints,
                backgroundColor: ['#5e001f','#00e1d9', '#f2bc94'],
            }],
    };

    window.myBar = new Chart(ctx, {
        type: 'doughnut',
        data: pieChartData,
        options: {
            legend: {
                display: false,
            },
        },
    });

    $('#client-invoices-container').
        append(setInvoiceTemplate(data.dataPoints, data.labels));
};

window.setInvoiceTemplate = function (
    projectStatusPercentages, projectStatusLabels) {
    return '<div class="row mt-3 py-2 justify-content-center">\n' +
        '       <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 text-center">\n' +
        '           <i class="fas fa-columns mt-3 invoiceStatusDraft"></i>\n' +
        '           <h3 class="font-weight-normal">\n' +
        '               <span>' + projectStatusPercentages[0] + ' %</span>\n' +
        '           </h3>\n' +
        '           <p class="text-muted mb-0">' + projectStatusLabels[0] +
        '</p>\n' +
        '       </div>\n' +
        '       <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 text-center">\n' +
        '           <i class="fas fa-columns mt-3 invoiceStatusSent"></i>\n' +
        '           <h3 class="font-weight-normal">\n' +
        '               <span>' + projectStatusPercentages[1] + ' %</span>\n' +
        '           </h3>\n' +
        '           <p class="text-muted mb-0">' + projectStatusLabels[1] +
        '</p>\n' +
        '       </div>\n' +
        '       <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 text-center">\n' +
        '           <i class="fas fa-columns mt-3 invoiceStatusPaid"></i>\n' +
        '           <h3 class="font-weight-normal">\n' +
        '               <span>' + projectStatusPercentages[2] + ' %</span>\n' +
        '           </h3>\n' +
        '           <p class="text-muted mb-0">' + projectStatusLabels[2] +
        '</p>\n' +
        '  </div>';
};
