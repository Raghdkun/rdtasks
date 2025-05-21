'use strict';

let $datePicker = $('#developers-report-date-picker')
const start = languageName == 'ar' ? moment().lang('en') : moment();

$(window).on('load', function () {
    loadDevelopersWorkReport(start.format('YYYY-MM-D  H:mm:ss'))
})

$datePicker.on('apply.daterangepicker', function (ev, picker) {
    let startDate = languageName == 'ar' ? picker.startDate.lang('en').format('YYYY-MM-D  H:mm:ss') : picker.startDate.format('YYYY-MM-D  H:mm:ss')
    loadDevelopersWorkReport(startDate)
})

window.cb = function (start) {
    $datePicker.find('span').html(start.format('MMM D, YYYY'))
}

cb(start)

$datePicker.daterangepicker({
    startDate: start,
    opens: 'left',
    maxDate: moment(),
    autoUpdateInput: false,
    singleDatePicker: true,
    locale:{
        customRangeLabel: Lang.get('messages.common.custom'),
        applyLabel:Lang.get('messages.common.apply'),
        cancelLabel: Lang.get('messages.common.cancel'),
        fromLabel:Lang.get('messages.common.from'),
        toLabel: Lang.get('messages.common.to'),
        monthNames: [
            Lang.get('messages.months.jan'),
            Lang.get('messages.months.feb'),
            Lang.get('messages.months.mar'),
            Lang.get('messages.months.apr'),
            Lang.get('messages.months.may'),
            Lang.get('messages.months.jun'),
            Lang.get('messages.months.jul'),
            Lang.get('messages.months.aug'),
            Lang.get('messages.months.sep'),
            Lang.get('messages.months.oct'),
            Lang.get('messages.months.nov'),
            Lang.get('messages.months.dec')
        ],
        daysOfWeek: [
            Lang.get('messages.weekdays.sun'),
            Lang.get('messages.weekdays.mon'),
            Lang.get('messages.weekdays.tue'),
            Lang.get('messages.weekdays.wed'),
            Lang.get('messages.weekdays.thu'),
            Lang.get('messages.weekdays.fri'),
            Lang.get('messages.weekdays.sat')
        ],
    },
}, cb)

window.loadDevelopersWorkReport = function (startDate) {
    $.ajax({
        type: 'GET',
        url: route('developers-work-report'),
        dataType: 'json',
        data: {
            start_date: startDate,
        },
        cache: false,
    }).done(prepareDeveloperWorkReport)
}

window.prepareDeveloperWorkReport = function (result) {
    $('#developers-daily-work-report-container').html('')
    let data = result.data
    if (data.totalRecords === 0) {
        $('#developers-daily-work-report-container').empty()
        $('#developers-daily-work-report-container').
            append(
                '<div align="center" class="no-record">'+ noRecordFoundMessage +'</div>')
        return true
    } else {
        $('#developers-daily-work-report-container').html('')
        $('#developers-daily-work-report-container').
            append('<canvas id="developers-daily-work-report"></canvas>')
    }
    let ctx = document.getElementById('developers-daily-work-report').
        getContext('2d')
    ctx.canvas.style.height = '500px'
    ctx.canvas.style.width = '100%'
    let dailyWorkReportChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.data.labels,
            datasets: [
                {
                    label: data.label,
                    data: data.data.data,
                    backgroundColor: data.data.backgroundColor,
                    borderWidth: 1,
                }],
        },
        options: {
            tooltips: {
                mode: 'index',
                callbacks: {
                    label: function (tooltipItem, data) {
                        let element = document.createElement('textarea');
                        element.innerHTML = data.datasets[tooltipItem.datasetIndex].label;
                        let label = element.value ||
                            ''

                        if (label) {
                            label += ': '
                        }
                        result = convertToTimeFormat(tooltipItem.yLabel)
                        return label + result
                    },
                },
            },
            scales: {
                yAxes: [
                    {
                        scaleLabel: {
                            display: true,
                            labelString: 'Hours',
                        },
                    }],
            },
            legend: { display: false },
        },
    })
}
window.convertToTimeFormat = function (duration) {
    const totalTime = duration.toString().split('.')
    const hours = parseInt(totalTime[0])
    const minutes = Math.floor((duration * 60)) - Math.floor((hours * 60))
    if (hours === 0) {
        return minutes + 'min'
    }

    if (minutes > 0) {
        return hours + 'hr ' + minutes + 'min'
    }
    return hours + 'hr'
}

