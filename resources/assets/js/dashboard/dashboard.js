'use strict';

$(document).ready(function () {
    $('#userId').select2({
        width: '100%',
        placeholder: 'Select User',
    })
})

let timeRange = $('#time_range')
const today = languageName == 'ar' ? moment().lang('en') : moment();
let start = today.clone().startOf('month')
let end = today.clone().endOf('month')
let userId = $('#userId').val()
let isPickerApply = false
$(window).on('load', function () {
    if (languageName == 'ar'){
        loadUserWorkReport(start.lang('en').format('YYYY-MM-D  H:mm:ss'),
            end.lang('en').format('YYYY-MM-D  H:mm:ss'), userId);
        loadHours(start.lang('en').format('YYYY-MM-D  H:mm:ss'),
            end.lang('en').format('YYYY-MM-D  H:mm:ss'), userId);
    }else{
        loadUserWorkReport(start.format('YYYY-MM-D  H:mm:ss'),
            end.format('YYYY-MM-D  H:mm:ss'), userId);
        loadHours(start.format('YYYY-MM-D  H:mm:ss'),
            end.format('YYYY-MM-D  H:mm:ss'), userId);
    }
})

timeRange.on('apply.daterangepicker', function (ev, picker) {
    isPickerApply = true
    start = languageName == 'ar' ? picker.startDate.lang('en').format('YYYY-MM-D  H:mm:ss') : picker.startDate.format('YYYY-MM-D  H:mm:ss')
    end = languageName == 'ar' ? picker.endDate.lang('en').format('YYYY-MM-D  H:mm:ss') : picker.endDate.format('YYYY-MM-D  H:mm:ss')
    loadUserWorkReport(start, end, userId);
    loadHours(start, end, userId);
})

window.cb = function (start, end) {
    if (languageName == 'ar') {
        timeRange.find('span').
            html(start.lang('en').format('MMM D, YYYY') + ' - ' + end.lang('en').format('MMM D, YYYY'))
    }else{
        timeRange.find('span').
            html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'))
    }
}

cb(start, end)

const lastMonth = moment().startOf('month').subtract(1, 'days')

timeRange.daterangepicker({
    startDate: start,
    endDate: end,
    opens: 'left',
    showDropdowns: true,
    autoUpdateInput: false,
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
    ranges: {
        [Lang.get('messages.days.today')]: [moment(), moment()],
        [Lang.get('messages.days.this_week')]: [
            moment().startOf('week'),
            moment().endOf('week')],
        [Lang.get('messages.days.last_week')]: [
            moment().startOf('week').subtract(7, 'days'),
            moment().startOf('week').subtract(1, 'days')],
        [Lang.get('messages.days.this_month')]: [start, end],
        [Lang.get('messages.days.last_month')]: [
            lastMonth.clone().startOf('month'),
            lastMonth.clone().endOf('month')],
    },
}, cb)

$('#userId').on('change', function (e) {
    e.preventDefault();
    userId = $('#userId').val();
    let startDate = (isPickerApply) ? start : start.format(
        'YYYY-MM-D  H:mm:ss');
    let endDate = (isPickerApply) ? end : end.format('YYYY-MM-D  H:mm:ss');
    loadUserWorkReport(startDate, endDate, userId);
    loadHours(startDate, endDate, userId);
});

window.loadHours = function (startDate, endDate, userId) {
    $.ajax({
        type: 'GET',
        url: route('dashboard-total-hours'),
        dataType: 'json',
        data: {
            start_date: startDate,
            end_date: endDate,
            user_id: userId,
        },
        cache: false,
        success: function (result) {
            $('.hours').empty();
            $('.hours').append('(' + result.data + ')');
        },
    });
};

window.loadUserWorkReport = function (startDate, endDate, userId) {
    $.ajax({
        type: 'GET',
        url: route('users-work-report'),
        dataType: 'json',
        data: {
            start_date: startDate,
            end_date: endDate,
            user_id: userId,
        },
        cache: false,
    }).done(prepareUserWorkReport)
}

window.prepareUserWorkReport = function (result) {
    $('#daily-work-report').html('')
    let data = result.data
    if (data.totalRecords === 0) {
        $('#work-report-container').html('')
        $('#work-report-container').
            append(
                '<div align="center" class="no-record">'+ noRecordFoundMessage +'</div>')
        return true
    } else {
        $('#work-report-container').html('')
        $('#work-report-container').
            append('<canvas id="daily-work-report"></canvas>')
    }

    let barChartData = {
        labels: data.date,
        datasets: data.data,
        total_hrs: data.totalHrs,
    };
    let ctx = document.getElementById('daily-work-report').getContext('2d');
    ctx.canvas.style.height = '400px';
    ctx.canvas.style.width = '100%';
    window.myBar = new Chart(ctx, {
        type: 'bar',
        data: barChartData,
        options: {
            title: {
                display: false,
                text: data.label,
            },
            tooltips: {
                mode: 'index',
                callbacks: {
                    title: function (tooltipItem, data) {
                        const labelDate = tooltipItem[0]['label'];

                        return labelDate + ' - ' + roundToQuarterHour(data.total_hrs[labelDate]);
                    },
                    label: function (tooltipItem, data) {
                     const result = roundToQuarterHour(tooltipItem.yLabel);
                        if (result === '0min') {
                            return ''
                        }
                        let element = document.createElement('textarea');
                        element.innerHTML = data.datasets[tooltipItem.datasetIndex].label;
                        let label = element.value || '';

                        if (label) {
                            label += ': '
                        }

                        return label + result
                    },
                },
            },
            responsive: false,
            maintainAspectRatio: false,
            scales: {

                xAxes: [
                    {
                        stacked: true,
                    }],
                yAxes: [
                    {
                        stacked: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Hours',
                        },
                        ticks: {
                            min: 0,
                            stepSize: 1,
                        },
                    }],
            },
        },
    })
};

window.roundToQuarterHour = function (duration) {
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

$(document).ready(function () {
    let applyBtn = $('.range_inputs > button.applyBtn');
    $(document).on('click','.ranges li', function () {
        if($(this).data('range-key') === 'Custom Range') {
            applyBtn.css('display','initial')
        } else {
            applyBtn.css('display','none')
        }
    });
    applyBtn.css('display','none')
})
