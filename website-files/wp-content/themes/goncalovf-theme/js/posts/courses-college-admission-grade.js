window.onload = function() {

    // Setup chart dimensions
    var margin = {top: 20, right: 30, bottom: 50, left: 60},
        width = 960 - margin.left - margin.right,
        height = 520 - margin.top - margin.bottom;

    var chart = d3.select(".chart")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom);

    var data,
        courses,
        gradeToShow,
        gradeToShowTitleText;

    // Setup scales and axes
    var x = d3.scale.ordinal()
        .rangeBands([0, width], 0.1, 0.1);

    var xAxis = d3.svg.axis()
        .scale(x)
        .orient("bottom");

    chart.append("g")
        .attr("class", "x axis")
        .attr("transform", "translate(" + margin.left + ", " + (height + margin.top) + ")")
        .call(xAxis)
      .append("text")
        .attr("dy", "-.71em")
        .attr("x", width)
        .attr("dx", -6)
        .style("text-anchor", "end")
        .text("Cursos");

    var y = d3.scale.linear()
        .range([height, 0])
        .domain([95, 200]);

    var yAxis = d3.svg.axis()
        .scale(y)
        .orient("left");

    chart.append("g")
        .attr("class", "y axis")
        .call(yAxis)
        .attr("transform", "translate(" + margin.left + ", " + margin.top + ")")
      .append("text")
        .attr("transform", "rotate(-90)")
        .attr("y", 6)
        .attr("dy", ".71em")
        .style("text-anchor", "end")
        .text("Média de ingresso");

    var color = d3.scale.category10();

    // Setup select2
    var courseSelectJquery = jQuery('#menu select[name=course]');
    courseSelectJquery.select2();
    courseSelectJquery.on("change", changeCourses);

    var gradeSelectJquery = jQuery('#menu select[name=grade]');
    gradeSelectJquery.on("change", changeGradeMetric);

    // Define the div for the tooltip
    var div = d3.select("body").append("div")
        .attr("class", "tooltip")
        .style('opacity', 0);

    //Add source
    chart.append('text')
        .attr('class', 'source')
        .attr("x", ( margin.left + width ))
        .attr("y", ( margin.top + height + margin.bottom))
        .attr("text-anchor", "end")
        .style("font-size", "10px")
        .text("Fonte: DGES");

    d3.csv("/wp-content/themes/goncalovf-theme/content/post-data/courses-college-admission-grade.csv", type, function(loadedData) {

        data = loadedData;

        data.forEach(function(d) {
            d.color = color(d.Academic_level);
        });

        var courseSelectD3 = d3.select("#menu select");
        courseSelectD3.selectAll("option")
            .data(d3.map(data, function(d){return d.Course;}).keys())       // this filters the courses to only show unique values
            .enter().append("option")
            .text(function(d) { return d; });


        // Add legend
        var legend = chart.selectAll(".legend")
            .data(color.domain())
            .enter().append("g")
            .attr("class", "legend")
            .attr("transform", function(d, i) { return "translate(0," + (i * 20 + margin.top) + ")"; });

        legend.append("rect")
            .attr("x", (margin.left + width - 18))
            .attr("width", 18)
            .attr("height", 18)
            .style("fill", color);

        legend.append("text")
            .attr("x", (margin.left + width - 24))
            .attr("y", 9)
            .attr("dy", ".35em")
            .style("text-anchor", "end")
            .text(function(d) { return d; });


        var availableCourses = ["Arquitetura", "Biologia", "Bioquímica", "Biotecnologia", "Ciências Biomédicas Laboratoriais", "Ciências da Comunicação", "Ciências do Desporto", "Contabilidade", "Economia", "Educação Básica", "Educação Social", "Enfermagem", "Engenharia Biomédica", "Engenharia Civil", "Engenharia do Ambiente", "Engenharia e Gestão Industrial", "Engenharia Eletrotécnica e de Computadores", "Engenharia Física", "Engenharia Informática", "Engenharia Mecânica", "Engenharia Química", "Farmácia", "Fisioterapia", "Geologia", "Gestão", "Gestão de Empresas", "História", "Imagem Médica e Radioterapia", "Línguas, Literaturas e Culturas", "Marketing", "Matemática", "Medicina", "Psicologia", "Serviço Social", "Sociologia", "Solicitadoria", "Turismo"];

        var shuffledAvailableCourses = availableCourses.sort(() => .5 - Math.random()); // shuffle
        courses = shuffledAvailableCourses.slice(0,5).sort() ; //get sub-array of first n elements AFTER shuffle

        courseSelectJquery.val(courses);

        gradeToShow = 'Admission_grade_2017';
        gradeToShowTitleText = '2017';

        // Update chart title
        chart.append("text")
            .attr('class', 'title')
            .attr("x", ( margin.left + width / 2))
            .attr("y", margin.top)
            .attr("text-anchor", "middle")
            .style("font-size", "16px")
            .style("font-weight", "bold")
            .text("Média de ingresso por curso em cada instituição pública, 1ª fase, " + gradeToShowTitleText);

        redraw()
    });


    function type(d) {
        d.Admission_grade_2017  = +d.Admission_grade_2017;  // coerce to number
        d.Admission_grade_avg   = +d.Admission_grade_avg;   // coerce to number
        return d;
    }


    function changeCourses() {
        courses = courseSelectJquery.val() || [];
        redraw();
    }

    function changeGradeMetric() {
        gradeToShow = gradeSelectJquery.val();
        gradeToShowTitleText = ( gradeToShow === 'Admission_grade_2017' ) ? '2017' : 'média entre 2017-2015';

        chart.select('.title')
            .text("Média de ingresso por curso em cada instituição pública, 1ª fase, " + gradeToShowTitleText);

        console.log(gradeToShow);

        redraw();
    }


    function redraw() {

        // Update xAxis
        x.domain(courses);
        d3.transition(chart).select(".x.axis")
            .call(xAxis);


        var bandWidth = x.rangeBand();

        var circle = chart.selectAll(".dot")
            .data(data.filter(function(d) { return courses.includes(d.Course) }))
            .attr("cx", function(d) { return x(d.Course) })
            .attr("cy", function(d) { return y(d[gradeToShow]) })
            .style("fill", function(d) { return d.color; })
            .attr("transform", "translate(" + (margin.left + bandWidth / 2)+ ", " + margin.top + ")");

        circle.exit().remove();

        circle.enter().append("circle")
            .attr("class", "dot")
            .attr("r", 4)
            .attr("cx", function(d) { return x(d.Course) })
            .attr("cy", function(d) { return y(d[gradeToShow]) })
            .style("fill", function(d) { return d.color; })
            .attr("transform", "translate(" + (margin.left + bandWidth / 2)+ ", " + margin.top + ")")
            .on("mouseover", function(d) {
                div.transition()
                    .duration(200)
                    .style('opacity', 1);
                div.html(d.Inst_fullname + '<br>Nota de ingresso 2017: ' + d.Admission_grade_2017 + '<br>Nota de ingresso (média 2017-2015): ' + d.Admission_grade_avg + '<br><a href="http://www.dges.gov.pt/guias/detcursopi.asp?codc=' + d.Course_code + '&code=' + d.Inst_code + '" target="_blank">Ver no guia de cursos</a> <i class="fas fa-external-link-alt"></i>')
                    .attr("class", "tooltip")
                    .style("left", (d3.event.pageX + 5) + "px")
                    .style("top", (d3.event.pageY - 28) + "px")
            })
            .on("mouseout", function() {
                d3.selectAll("div.tooltip")
                    .transition()
                    .delay(1500)
                    .duration(200)
                    .style('opacity', 0);
            });


        // Prevent xAxis labels from overlapping
        chart.selectAll('.tick text')
            .call(wrap, bandWidth);


        // Add force to prevent circles from overlapping
        var force = d3.layout.force()
            .nodes(data.filter(function(d) { return courses.includes(d.Course) }))
            .size([width, height])
            .on("tick", tick)
            .charge(-1)
            .gravity(0)
            .chargeDistance(20);

        force.start();

        function tick(e) {
            d3.selectAll('.dot').each(moveTowardDataPosition(e.alpha));

            d3.selectAll('.dot').attr("cx", function(d) { return d.x; })
                .attr("cy", function(d) { return d.y; });

        }

        function moveTowardDataPosition(alpha) {
            return function(d) {
                d.x += (x(d.Course) - d.x) * 0.1 * alpha;
                d.y += (y(d[gradeToShow]) - d.y) * 0.1 * alpha;
            };
        }

        // Make y axis labels well positioned, before the ticks
        chart.selectAll('.y.axis tspan')
            .attr('x', -10);

    }

    function wrap(text, bandWidth) {
        text.each(function() {
            var text = d3.select(this),
                words = text.text().split(/\s+/).reverse(),
                word,
                line = [],
                lineNumber = 0,
                lineHeight = 1.1, // ems
                y = text.attr("y"),
                dy = parseFloat(text.attr("dy")),
                tspan = text.text(null).append("tspan").attr("x", 0).attr("y", y).attr("dy", dy + "em");
            while (word = words.pop()) {
                line.push(word);
                tspan.text(line.join(" "));
                if (tspan.node().getComputedTextLength() > bandWidth) {
                    line.pop();
                    tspan.text(line.join(" "));
                    line = [word];
                    tspan = text.append("tspan").attr("x", 0).attr("y", y).attr("dy", ++lineNumber * lineHeight + dy + "em").text(word);
                }
            }
        });
    }

};