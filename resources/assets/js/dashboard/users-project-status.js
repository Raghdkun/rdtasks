'use strict';

$(window).on('load', function () {
    loadUsersProjectStatus();
});

window.loadUsersProjectStatus = function () {
    $.ajax({
        type: 'GET',
        url: projectStatusUrl,
        cache: false,
    }).done(prepareUsersProjectStatusChart);
};

window.prepareUsersProjectStatusChart = function (result) {
    $('#users-project-status-container').html('');
    let data = result.data;
    if (data.totalRecords.length === 0) {
        $('#users-project-status-container').empty();
        $('#users-project-status-container').append(
            '<div align="center" class="no-record">'+ noRecordFoundMessage +'</div>');
        return true;
    } else {
        $('#users-project-status-container').html('');
        $('#users-project-status-container').
            append('<canvas id="users-project-status"></canvas>');
    }
    let ctx = document.getElementById('users-project-status').getContext('2d');
    ctx.canvas.style.height = '350px';
    ctx.canvas.style.width = '100%';
    let pieChartData = {
        labels: data.labels,
        datasets: [
            {
                data: data.dataPoints,
                backgroundColor: ['#6677ef', '#47c363', '#fc544b','#3abaf4'],
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

    $('#users-project-status-container').
        append(setStatusTemplate(data.dataPoints, data.labels));
};

window.setStatusTemplate = function (
    projectStatusPercentages, projectStatusLabels) {
    return '<div class="row text-center mt-3 py-2">\n' +
        '       <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12 pr-0 pl-0">\n' +
        '           <i class="fas fa-chart-line mt-3 projectStatusOnGoing"></i>\n' +
        '           <h3 class="font-weight-normal">\n' +
        '               <span>' + projectStatusPercentages[0] + ' %</span>\n' +
        '           </h3>\n' +
        '           <p class="text-muted mb-0">' + projectStatusLabels[0] +
        '</p>\n' +
        '       </div>\n' +
        '       <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12 pr-0 pl-0">\n' +
        '           <i class="fas fa-chart-line mt-3 projectStatusFinished"></i>\n' +
        '           <h3 class="font-weight-normal">\n' +
        '               <span>' + projectStatusPercentages[1] + ' %</span>\n' +
        '           </h3>\n' +
        '           <p class="text-muted mb-0">' + projectStatusLabels[1] +
        '</p>\n' +
        '       </div>\n' +
        '       <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12 pr-0 pl-0">\n' +
        '           <i class="fas fa-chart-line mt-3 projectStatusOnHold"></i>\n' +
        '           <h3 class="font-weight-normal">\n' +
        '               <span>' + projectStatusPercentages[2] + ' %</span>\n' +
        '           </h3>\n' +
        '           <p class="text-muted mb-0">' + projectStatusLabels[2] +
        '</p>\n' +
        '       </div>\n' +
        '<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12 pr-0 pl-0">\n' +
        '           <i class="fas fa-chart-line mt-3 projectStatusArchived"></i>\n' +
        '           <h3 class="font-weight-normal">\n' +
        '               <span>' + projectStatusPercentages[3] + ' %</span>\n' +
        '           </h3>\n' +
        '           <p class="text-muted mb-0">' + projectStatusLabels[3] +
        '</p>\n' +
        '       </div>\n' +
        '  </div>';
};
