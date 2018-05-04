$.fn.barChart = function(json, domElement, transKey) {
    var margin = {top: 20, right: 20, bottom: 30, left: 40},
    width = 650 - margin.left - margin.right,
    height = 300 - margin.top - margin.bottom;

    var x = d3.scale.ordinal()
      .rangeRoundBands([0, width], .1);

    var y = d3.scale.linear()
      .range([height, 0]);

    var xAxis = d3.svg.axis()
      .scale(x)
      .orient("bottom");

    var yAxis = d3.svg.axis()
      .scale(y)
      .orient("left")
      .ticks(10);

    var svg = d3.select(domElement).append("svg")
      .attr("width", width + margin.left + margin.right)
      .attr("height", height + margin.top + margin.bottom)
    .append("g")
      .attr("transform", "translate(" + margin.left + "," + margin.top + ")");


    x.domain(json.map(function(d) { return generateLabel(d) }));
    y.domain([0, d3.max(json, function(d) { return d.count; })]);

    svg.append("g")
        .attr("class", "x axis")
        .attr("transform", "translate(0," + height + ")")
        .call(xAxis);

    svg.append("g")
        .attr("class", "y axis")
        .call(yAxis);

    svg.selectAll(".bar")
        .data(json)
      .enter().append("rect")
        .attr("class", "bar")
        .attr("x", function(d) { return x(generateLabel(d)) })
        .attr("width", x.rangeBand())
        .attr("y", function(d) { return y(d.count); })
        .attr("height", function(d) { return height - y(d.count); })
        .append("title").text(function(d) { return Translator.transChoice(transKey, d.count) });

    function generateLabel(d) {
        if (typeof d.percent !== "undefined") {
            return (d.abscissa.length > 19 ? d.abscissa.substring(0, 16).trim() + '...' : d.abscissa) + d.percent;
        } else {
            return d.abscissa;
        }
    }
};
