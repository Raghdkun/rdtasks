'use strict';

$(window).on('load', function () {
    loadClientProjectsStatus();
});

window.loadClientProjectsStatus = function () {
    $.ajax({
        type: 'GET',
        url: route('project-status'),
        cache: false,
    }).done(prepareClientProjectStatusChart);
};

window.prepareClientProjectStatusChart = function (result) {
    $('#project-status-container').html('');
    let data = result.data;
    if (data.dataPoints.every(item => item === 0)) {
        $('#project-status-container').empty();
        $('#project-status-container').append(
            '<div align="center" class="no-record">No Records Found</div>');
        return true;
    } else {
        $('#project-status-container').html('');
        $('#project-status-container').
            append('<canvas id="client-project-status"></canvas>');
    }
    let ctx = document.getElementById('client-project-status').getContext('2d');
    ctx.canvas.style.height = '250px';
    ctx.canvas.style.width = '100%';
    let pieChartData = {
        labels: data.labels,
        datasets: [
            {
                data: data.dataPoints,
                backgroundColor: ['#D2B4DE', '#F5CBA7', '#A9DFBF','#a9c0cb'],
            }],
    };

    window.myBar = new Chart(ctx, {
        type: 'pie',
        data: pieChartData,
        options: {
            legend: {
                display: false,
            },
        },
    });

    $('#project-status-container').
        append(setStatusTemplate(data.dataPoints, data.labels));
};

window.setStatusTemplate = function (
    projectStatusPercentages, projectStatusLabels) {
    return '<div class="row text-center mt-3 py-2">\n' +
        '       <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12">\n' +
        '           <i class="fas fa-chart-line mt-3 clientProjectStatusFinished"></i>\n' +
        '           <h3 class="font-weight-normal">\n' +
        '               <span>' + projectStatusPercentages[0] + ' %</span>\n' +
        '           </h3>\n' +
        '           <p class="text-muted mb-0">' + projectStatusLabels[0] +
        '</p>\n' +
        '       </div>\n' +
        '       <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12">\n' +
        '           <i class="fas fa-chart-line mt-3 clientProjectStatusOnGoing"></i>\n' +
        '           <h3 class="font-weight-normal">\n' +
        '               <span>' + projectStatusPercentages[1] + ' %</span>\n' +
        '           </h3>\n' +
        '           <p class="text-muted mb-0">' + projectStatusLabels[1] +
        '</p>\n' +
        '       </div>\n' +
        '       <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12">\n' +
        '           <i class="fas fa-chart-line mt-3 clientProjectStatusOnHold"></i>\n' +
        '           <h3 class="font-weight-normal">\n' +
        '               <span>' + projectStatusPercentages[2] + ' %</span>\n' +
        '           </h3>\n' +
        '           <p class="text-muted mb-0">' + projectStatusLabels[2] +
        '</p>\n' +
        '       </div>\n' +
        '<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12">\n' +
        '           <i class="fas fa-chart-line mt-3 clientProjectStatusArchived"></i>\n' +
        '           <h3 class="font-weight-normal">\n' +
        '               <span>' + projectStatusPercentages[3] + ' %</span>\n' +
        '           </h3>\n' +
        '           <p class="text-muted mb-0">' + projectStatusLabels[3] +
        '</p>\n' +
        '       </div>\n' +
        '  </div>';
};
