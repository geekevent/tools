google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    let data = google.visualization.arrayToDataTable(moment);

    let options = {
        title: 'Affluence',
        subtitle: 'Evolution du nombre de personne présente dans l\'espace en fonction du moment',
        curveType: 'function',
        legend: { position: 'bottom' }
    };

    let chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

    chart.draw(data, options);
}