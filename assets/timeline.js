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
        //TODO pravi podaci
        [ 'Taken', 'someone', new Date(0,0,0,12,0,0), new Date(0,0,0,13,0,0)],
   ]);

    var options = {
        timeline: { showRowLabels: false },
        avoidOverlappingGridLines: true,
        colors : ['#e8351e']
    };

    chart.draw(dataTable, options);
}