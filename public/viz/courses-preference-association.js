/*
 *  Relationship between university courses based on students' application preferences when applying to college, Portugal, 2017
 *
 *  Remixed from Michael Currie, @ https://bl.ocks.org/MichaelCurrie/5e2da378a53ea624082cb55e78fdfa05
 *
 */

var char_dim_width,
    height_shift;

if (screen.width < 800) {
    char_dim_width = screen.width;
    height_shift = 75;
} else {
    char_dim_width = 688;
    height_shift = 25;
}

var PARAM = {
    "data_location": "/wp-content/themes/goncalovf-theme/content/post-data/courses-preference-association.csv",
    "report_title": "Associação de cursos nas preferências dos alunos, 1ª fase, 2017",
    "footer_text": "Fonte: DGES.",
    "margins": {
        "top": 20,
        "right": 30,
        "bottom": 50,
        "left": 50
    },
    "chart_dim": {
        "width": char_dim_width,
        "height": 520,
        "height_shift": height_shift
    },
    "node_attr": {
        "id": "rel",
        "radius": "points",
        "fill_color": "rel_bagg"
    },
    "radiusScale_range": {
        "min": 2,
        "max": 75
    },
    "force_strength": 15
};


function createBubbleChart() {

    var ref,
        width,
        height,
        chart,
        radiusScale,
        fillColorScale,
        data,
        filtered_data,
        node,
        nodes,
        bubbles;

    var tooltip = d3.select("body").append("div")
            .attr("class", "tooltip")
            .style('opacity', 0);

    // Setup Select2
    var courseSelectJquery = jQuery('#menu select[name=ref]')
        .select2()
        .on("change", changeRef);

    function createCanvas() {

        width = PARAM.chart_dim.width - PARAM.margins.left - PARAM.margins.right;
        height = PARAM.chart_dim.height - PARAM.margins.top - PARAM.margins.bottom;

        chart = d3.select(".chart")
            .attr("width", PARAM.chart_dim.width)
            .attr("height", PARAM.chart_dim.height);

    }

    function createNodes(filtered_data) {

        var myNodes = filtered_data.map(function (d) {

            node = {
                ref: d.ref,
                id: d[PARAM.node_attr.id],
                scaled_radius: radiusScale(+d[PARAM.node_attr.radius]),
                actual_radius: +d[PARAM.node_attr.radius],
                fill_color_group: d[PARAM.node_attr.fill_color],
                course: d.rel_name
            };

            return node;

        });

        return myNodes;
    }

    var bubbleChart = function bubbleChart( rawData ) {

        data = rawData;

        d3.select("#menu select[name=ref]").selectAll("option")
          .data(d3.map(rawData, function(d){return d.ref_name;}).keys())       // this filters the courses to only show unique values
          .enter().append("option")
            .attr("value", function (d) { return d; })
            .text(function(d) { return d; });

        // Default course to show
        var availableCourses = ["Arquitetura", "Biologia", "Bioquímica", "Biotecnologia", "Ciências Biomédicas Laboratoriais", "Ciências da Comunicação", "Ciências do Desporto", "Contabilidade", "Economia", "Educação Básica", "Educação Social", "Enfermagem", "Engenharia Biomédica", "Engenharia Civil", "Engenharia do Ambiente", "Engenharia e Gestão Industrial", "Engenharia Eletrotécnica e de Computadores", "Engenharia Física", "Engenharia Informática", "Engenharia Mecânica", "Engenharia Química", "Farmácia", "Fisioterapia", "Geologia", "Gestão", "Gestão de Empresas", "História", "Imagem Médica e Radioterapia", "Línguas, Literaturas e Culturas", "Marketing", "Matemática", "Medicina", "Psicologia", "Serviço Social", "Sociologia", "Solicitadoria", "Turismo"];

        ref = availableCourses[Math.floor(Math.random() * availableCourses.length)];

        courseSelectJquery.val(ref);

        fillColorScale = d3.scaleOrdinal(d3.schemeCategory10)
            .domain( d3.map(rawData, function(d){return d[PARAM.node_attr.fill_color];}).keys() );

        createCanvas();

        redraw();

    };

    function redraw() {

        filtered_data = filterData();

        addLegend( filtered_data );

        var maxRadius = d3.extent(filtered_data, function (d) { return +d[PARAM.node_attr.radius] })[1];

        radiusScale = d3.scaleLinear()
            .range([PARAM.radiusScale_range.min, PARAM.radiusScale_range.max])
            .domain([0, maxRadius]);

        nodes = createNodes(filtered_data);

        // Create the bubbles and the force holding them apart
        createBubbles();

    }

    function filterData() {

        var filtered_data = data.filter(function(d) { return d.ref_name === ref });

        filtered_data.sort(function(a, b) {
            return parseFloat(b.points) - parseFloat(a.points);
        });

        return filtered_data.slice(0, 30);
    }

    function addLegend( filtered_data ) {

        var legend_data = d3.map(filtered_data, function(d){return d[PARAM.node_attr.fill_color] }).keys();

        var rect = chart.selectAll(".legend_rect")
          .data( legend_data );

        rect.exit().remove();

        rect.enter()
          .append("rect")
            .attr("class", "legend_rect")
            .attr("x", (PARAM.margins.left + width - 18))
            .attr("transform", function(d, i) { return "translate(0," + (i * 20 + PARAM.margins.top) + ")" })
            .attr("width", 18)
            .attr("height", 18)
          .merge(rect)
            .style("fill", function(d) { return fillColorScale(d) });

        var text = chart.selectAll(".legend_text")
          .data( legend_data );

        text.exit().remove();

        text.enter()
          .append("text")
            .attr("class", "legend_text")
            .attr("x", (PARAM.margins.left + width - 24))
            .attr("transform", function(d, i) { return "translate(0," + (i * 20 + PARAM.margins.top) + ")" })
            .attr("y", 9)
            .attr("dy", ".35em")
            .style("text-anchor", "end")
          .merge(text)
            .text( function(d) { return d; } );

    }

    function createBubbles() {

        if (forceSim) {
            forceSim.stop(); // Stop any forces currently in progress
        }

        bubbles = chart.selectAll('.bubble')
          .data(nodes);

        var bubblesEnter = bubbles.enter()
          .append('circle')
            .attr('class', "bubble")
            .attr('stroke-width', 1)
            .on("mouseover", function(d) {
                d3.select(this).attr('stroke-width', 2);
                tooltip.transition()
                    .duration(200)
                    .style('opacity', 1);
                tooltip.html(d.course + '<br><a href="http://www.dges.gov.pt/guias/indcurso.asp?curso=' + d.id + '" target="_blank">Ver no guia de cursos</a> <i class="fas fa-external-link-alt"></i>')
                    .attr("class", "tooltip")
                    .style("left", (d3.event.pageX + 5) + "px")
                    .style("top", (d3.event.pageY + 5) + "px")
            })
            .on("mouseout", function() {
                d3.select(this).attr('stroke-width', 1);
                d3.selectAll("div.tooltip")
                    .transition()
                    .delay(1500)
                    .duration(200)
                    .style('opacity', 0);
            });

        bubbles = bubbles.merge(bubblesEnter)
            .attr('r', 0) // Initially, their radius (r attribute) will be 0.
            .attr('fill', function (d) {
                return fillColorScale(d.fill_color_group)
            })
            .attr('stroke', function (d) {
                return d3.rgb(fillColorScale(d.fill_color_group)).darker()
            });

        bubbles
            .exit()
            .remove();

        bubbles.transition()
            .duration(2000)
            .attr('r', function (d) { return d.scaled_radius });

        var forceSim = d3.forceSimulation()
          .nodes(nodes)
            .velocityDecay(0.3)
            .on("tick", tick)
            .force('charge', d3.forceManyBody().strength(+PARAM.force_strength))
            .force('center', d3.forceCenter( width / 2, height / 2 + PARAM.chart_dim.height_shift))
            .force('collision', d3.forceCollide().radius(function(d) {
                return d.scaled_radius
            }));

    }

    function tick() {
        bubbles.each(function (node) {})
            .attr("cx", function(d) { return d.x; })
            .attr("cy", function(d) { return d.y; });
    }

    function changeRef() {
        ref = courseSelectJquery.val() || [];
        redraw();
    }

    return bubbleChart;

}

window.onload = function() {

    // Create a new bubble chart instance
    var myBubbleChart = createBubbleChart();

    // Load data
    d3.csv( PARAM.data_location ).then(function( rawData ) {

        myBubbleChart(rawData);

    });

};
