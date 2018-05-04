$.fn.calenderHeatmap = function(jsonCalendar) {
    var cpt = 0;
    var length = Object.keys(jsonCalendar).length;
    $.each(jsonCalendar, function( year, timestamps ) {
        cpt++;
        var classes = cpt === length ? 'current' : 'hidden';
        var cal = new CalHeatMap();
        $('.calendars').append('<div class="panel '+ classes +'" id="calendar_'+year+'"></div>');
        cal.init({
            itemSelector : '#calendar_'+year,
            data: timestamps,
            domain: "month",
            subDomain: "day",
            start: new Date(year, 0),
            legend: [0, 5, 15, 30],
            legendHorizontalPosition: "right",
            displayLegend : true,
            cellSize: 19
        })
    });
};
