import $ from 'jquery';
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
const routes = require('./routes.json');
Routing.setRoutingData(routes);

const startDay = $('#meeting_form_start_date_day').get(0);
const startMonth = $('#meeting_form_start_date_month').get(0);
const startYear = $('#meeting_form_start_date_year').get(0);
const endDay = $('#meeting_form_end_date_day').get(0);
const endMonth = $('#meeting_form_end_date_month').get(0);
const endYear = $('#meeting_form_end_date_year').get(0);
let data;
getRoomAvailabilityData();

startDay.addEventListener('change', ()=>{
    endDay.value = startDay.value;
    getRoomAvailabilityData();
});
startMonth.addEventListener('change', ()=>{
    endMonth.value = startMonth.value;
    getRoomAvailabilityData();
});
startYear.addEventListener('change', ()=>{
    endYear.value = startYear.value;
    getRoomAvailabilityData();
});

function getRoomAvailabilityData(){
    data = null;
    let day, month, year;
    year = startYear.value;
    if (startDay.value.length === 1) {
        day = "0" + startDay.value;
    } else {
        day = startDay.value;
    }
    if (startMonth.value.length === 1) {
        month = "0" + startMonth.value;
    } else {
        month = startMonth.value;
    }
    let date = year + "-" + month + "-" + day;
    let id = $('#roomID').val();

    $.ajax({
        url: Routing.generate('app_room_availability'),
        method: 'POST',
        dataType: 'JSON',
        data:{
            'id' : id,
            'date' : date,
        },
        async: false,
        success: (response) => {
            try {
                data = JSON.parse(response);
                console.log(data);
            } catch (e) {
                console.log('Could not parse');
            }
        }
    });
    if (data) {
        drawChart();
    } else {
        document.getElementById('timelineList').innerText = 'Nema zakazanih sastanaka';
    }
}

/*chart for displaying availability of room*/
google.charts.load("current", {packages:["timeline"]});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    var container = document.getElementById('timelineList');
    container.innerText = null;
    var chart = new google.visualization.Timeline(container);
    var dataTable = new google.visualization.DataTable();

    dataTable.addColumn({ type: 'string', id: 'Room' });
    dataTable.addColumn({ type: 'string', id: 'Person' });
    dataTable.addColumn({ type: 'date', id: 'Start' });
    dataTable.addColumn({ type: 'date', id: 'End' });

    var options = {
        timeline: { showRowLabels: false },
        avoidOverlappingGridLines: true,
        colors : ['#e8351e']
    };

    data.forEach((val) => {
        let startTime = val.start.split(':');
        let endTime = val.end.split(':');
        dataTable.addRow([
            'Taken',
            val.creator,
            new Date(0, 0, 0, parseInt(startTime[0]), parseInt(startTime[1]), 0),
            new Date(0, 0, 0, parseInt(endTime[0]), parseInt(endTime[1]), 0)
        ]);
    });

    chart.draw(dataTable, options);
}