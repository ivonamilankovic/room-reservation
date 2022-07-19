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
    let date = startYear.value + "-" + startMonth.value + "-" + startDay.value;
    let id = $('#roomID').val();

    $.ajax({
        url: Routing.generate('app_room_availability'),
        method: 'POST',
        dataType: 'JSON',
        data:{
            'id' : id,
            'date' : date,
        },
        success: (response)=>{
            console.log(response);
        }
    });
}

/*chart for displaying availability of room*/
google.charts.load("current", {packages:["timeline"]});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    var container = document.getElementById('timelineList');
    var chart = new google.visualization.Timeline(container);
    var dataTable = new google.visualization.DataTable();

    dataTable.addColumn({ type: 'string', id: 'Room' });
    dataTable.addColumn({ type: 'string', id: 'Person' });
    dataTable.addColumn({ type: 'date', id: 'Start' });
    dataTable.addColumn({ type: 'date', id: 'End' });
    dataTable.addRows([
        //TODO pravi podaci koji se dobiju preko ajaxa
        [ 'Taken', 'someone', new Date(0,0,0,12,0,0), new Date(0,0,0,13,0,0)],
    ]);

    var options = {
        timeline: { showRowLabels: false },
        avoidOverlappingGridLines: true,
        colors : ['#e8351e']
    };

    chart.draw(dataTable, options);
}