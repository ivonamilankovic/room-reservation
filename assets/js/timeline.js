import $ from 'jquery';
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
const routes = require('./routes.json');
Routing.setRoutingData(routes);

const checkBoxesUsers = document.getElementsByClassName('form-check');
const startDay = $('#meeting_form_start_date_day').get(0);
const startMonth = $('#meeting_form_start_date_month').get(0);
const startYear = $('#meeting_form_start_date_year').get(0);
const startHour = $('#meeting_form_start_time_hour').get(0);
const startMinute = $('#meeting_form_start_time_minute').get(0);
const endDay = $('#meeting_form_end_date_day').get(0);
const endMonth = $('#meeting_form_end_date_month').get(0);
const endYear = $('#meeting_form_end_date_year').get(0);
const endHour = $('#meeting_form_end_time_hour').get(0);
const endMinute = $('#meeting_form_end_time_minute').get(0);
let data, startDateTime, endDateTime;
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
startHour.addEventListener('change', ()=>{
    getRoomAvailabilityData();
});
startMinute.addEventListener('change', ()=>{
    endYear.value = startYear.value;
    getRoomAvailabilityData();
});
endDay.addEventListener('change', ()=>{
    startDay.value = endDay.value;
    getRoomAvailabilityData();
});
endMonth.addEventListener('change', ()=>{
    startMonth.value = endMonth.value;
    getRoomAvailabilityData();
});
endYear.addEventListener('change', ()=>{
    startYear.value = endYear.value;
    getRoomAvailabilityData();
});
endHour.addEventListener('change', ()=>{
    getRoomAvailabilityData();
});
endMinute.addEventListener('change', ()=>{
    getRoomAvailabilityData();
});

function getValue(input){
    if (input.value.length === 1) {
        return  "0" + input.value;
    } else {
        return input.value;
    }
}
function getDates(){
    let sDay, sMonth, sHour, sMin;
    sDay = getValue(startDay);
    sMonth = getValue(startMonth);
    sHour = getValue(startHour);
    sMin = getValue(startMinute);
    startDateTime = startYear.value + '-' + sMonth + '-' + sDay + ' ' + sHour + ':' + sMin + ':00';
    let eDay, eMonth, eHour, eMin;
    eDay = getValue(endDay);
    eMonth = getValue(endMonth);
    eHour = getValue(endHour);
    eMin = getValue(endMinute);
    endDateTime = endYear.value + '-' + eMonth + '-' + eDay + ' ' + eHour + ':' + eMin + ':00';
}
function getUserAvailabilityData(checkBoxLabel){
    Array.from(checkBoxesUsers).forEach((chkBox)=>{
        while(chkBox.lastElementChild.lastElementChild){
            chkBox.lastElementChild.removeChild(chkBox.lastElementChild.lastElementChild);
        }
    });
    getDates();
    let userID = checkBoxLabel.getAttribute('for').substr(19);
    $.ajax({
        url: Routing.generate('app_user_availability'),
        method: 'POST',
        dataType: 'JSON',
        data:{
            'userID': userID,
            'start' : startDateTime,
            'end': endDateTime,
        },
        success:(r)=>{
            var arr = JSON.parse(r);
            console.log(arr); //??? nesto nije okej
            if(arr.length !== 0){

                var tag = document.createElement('span');
                var text = document.createTextNode('   *zauzet/a');
                tag.appendChild(text);
                tag.style.color = 'red';
                checkBoxLabel.appendChild(tag);
            }
        }
    });
}
function getRoomAvailabilityData(){
    let day, month, year;
    year = startYear.value;
    day = getValue(startDay);
    month = getValue(startMonth);
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
                drawChart();
            } catch (e) {
                console.log('Could not parse');
            }
        }
    });
    Array.from(checkBoxesUsers).forEach((chkBox)=>getUserAvailabilityData(chkBox.lastElementChild));
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

    var options = {
        timeline: { showRowLabels: false },
        avoidOverlappingGridLines: true
    };

    dataTable.addRow([
        'Radno vreme',
        'Radno vreme',
        new Date(0,0,0,8,0,0),
        new Date(0,0,0,20,0,0)
    ]);

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