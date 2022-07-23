import $ from 'jquery';
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
const routes = require('./routes.json');
Routing.setRoutingData(routes);

//setting today's date for default value
var date = new Date();
var day = date.getDate();
if (day < 10) day = "0" + day;
var month = date.getMonth() + 1;
if (month < 10) month = "0" + month;
var today = date.getFullYear() + "-" + month + "-" + day;

const dateField = document.getElementById('date');
dateField.addEventListener('change', getSchedule);
dateField.value = today;
let data, heightValue = 200;
getSchedule();

function getSchedule(){
    $.ajax({
        url: Routing.generate('app_get_schedule'),
        method: 'post',
        dataType: 'JSON',
        async: false,
        data:{
            'date' : dateField.value
        },
        success: (response)=>{
            data = JSON.parse(response);
            drawChart();
        },
        error:(msg)=>{
            console.log('Error message: '+msg);
        }
    });
}

google.charts.load("current", {packages:["timeline"]});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    var container = document.getElementById('scheduleTimeline');
    var chart = new google.visualization.Timeline(container);
    var dataTable = new google.visualization.DataTable();

    dataTable.addColumn({type: 'string', id: 'Room'});
    dataTable.addColumn({type: 'string', id: 'Person'});
    dataTable.addColumn({type: 'date', id: 'Start'});
    dataTable.addColumn({type: 'date', id: 'End'});

    var options = {
        avoidOverlappingGridLines: true,
        colors: ['#e8351e']
    };

    dataTable.addRow([
        'Radno vreme',
        '',
        new Date(0,0,0,8,0,0),
        new Date(0,0,0,20,0,0)
    ]);

    data.forEach((item)=>{
        heightValue += 20; //da se ne pojavi overflow
        let startTime = item.start.split(':');
        let endTime = item.end.split(':');
        dataTable.addRow([
            'Sala \'' + item.room + '\'',
            item.creator,
            new Date(0, 0, 0, parseInt(startTime[0]), parseInt(startTime[1]), 0),
            new Date(0, 0, 0, parseInt(endTime[0]), parseInt(endTime[1]), 0)
        ]);
    });

    container.style.height = heightValue + 'px';

    chart.draw(dataTable, options);

}


