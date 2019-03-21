jQuery(document).ready(function($){
	

	//console.log(standings);

	// dimensions
	var margin = {top: 30, right: 20, bottom: 70, left: 70};	
	var height = 300;
	
	var width;
	var divHeight;
	var legendPosX;
	var legendPosY;
	var widthLegend = 200; 
	
	var divWidth = parseInt(d3.select('#chart').style('width'), 10);
	//console.log(divWidth);

    width = divWidth - margin.left - margin.right - widthLegend;
    //height = divWidth / 2;
    divHeight = height + margin.top + margin.bottom;
    legendPosX = divWidth - widthLegend;
    legendPosY = margin.top;
	
	// time duration for changing dataset
	var yDelay = 500;
	var emphasizeDelay = 100;

	var x = d3.scale.ordinal()
		.rangePoints([0, width]);

	var y = d3.scale.linear()
		.range([height, 0]);

	var color = d3.scale.category20c();

	var gridColor = "#999";
	var gridStroke = "1";
	
	var yellow = '#FDDF12';
	var blue = '#2D3891';

	var xAxis = d3.svg.axis()
		.scale(x)
		.orient("bottom");

	var yAxis = d3.svg.axis()
		.scale(y)
		.tickSize(-10, 0, 0)
		//.tickValues(y.domain().filter(function(d, i) { return 1 ; }))
		.orient("left");

	// using ticks as workaround for grid lines
	var xGrid = d3.svg.axis()
		.scale(x)
		.orient("bottom")
		.tickSize(-height, 0, 0)
		.tickFormat("")
		.ticks(10);

	

	// line, that is bound to game data
	var valueline = d3.svg.line()
		.x(function(d) { return x(d.x); })
		.y(function(d) { return y(d.y); });

	// create the svg for chart
	var chartsvg;
	// svg background
	var chartBackground;
	// Append 'g' in a place that is the actual area for the graph
	var chart;
	// group for legend
	var legend;

	// url to get the chart data
	var url = "index.php?option=com_hbteam&task=getStandings4Chart&format=raw"
			+ "&teamkey=" + teamkey + "&season=" + season;
	//console.log(url);
	
	var standings;
	var home;
	
	function setUpChart () {
		// create the svg for chart
		chartsvg = d3.select("#chart").append("svg")
				.attr("width", divWidth)
				.attr("height", divHeight)
				.style("fill", "none");

		// svg background
		chartBackground = chartsvg.append("rect")
			.attr("width", "100%")
			.attr("height", "100%");

		// Append 'g' in a place that is the actual area for the graph
		chart = chartsvg.append("g")
			.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
	}
	
	d3.json(url, function(error, data) {
		//console.log(data);
		if (typeof data != 'undefined') {
			
			setUpChart();
			
			standings = data.standings;

			standings.forEach(function(d,i) { 
				if (d.name.indexOf(data.info.home) > -1) home = i; 
			}); 
			//console.log(home);

			x.domain(standings[0].data.map(function(r) {return r.date;}));
			y.domain([standings.length, 1]);

			// draw chart
			buildAxis();
			buildLegend(standings);
			populateData(standings);

			emphasizeTeam(home);

			// resizing for responsive chart
			d3.select(window).on('resize', function() { resize(standings); });
		} else {
			console.log('No Standings data');
		}
	});
	
	function resize(data) {

		// update width
		divWidth = parseInt(d3.select('#chart').style('width'), 10);
		//console.log(divWidth);
		
		if (divWidth > 0) { 
			width = divWidth - margin.left - margin.right - widthLegend;
			//height = divWidth / 2;
			divHeight = height + margin.top + margin.bottom;
			legendPosX = divWidth - widthLegend;
			legendPosY = margin.top;
		} else {
			width = divWidth - margin.left - margin.right;
			//height = divWidth / 2;
			divHeight = height + margin.top + margin.bottom + legendElementHeight * data.players.length;
			legendPosX = margin.left;
			legendPosY = height + margin.top + margin.bottom;
		}
		
		x.rangePoints([0, width]);
		// y.range([height, 0]);

		// resize the chart
		chartsvg
			.attr('height', divHeight + 'px')
			.attr('width', divWidth + 'px');

		// change the line
		updateData(data, 0);

		// change the x axis
		chart.select(".x.axis > text")
			.attr("x", width/2)
			.attr("y", margin.bottom-18);
	
		chart.select(".x.axis")
			.call(xAxis)
				.selectAll("g.tick text")
					.style("text-anchor", "end")
					.style("fill", "#000")
					.style("font-size", "14px")
					//.style("font-weight", "bold")
//					.attr("dx", "-2em")
					.attr("dy", "1.5em")
					//.attr("transform", function() {	return "rotate(-90)" })
					.text(function(d,i) { return (i+1); });
			
		
		
		chart.selectAll("line.minor")
            .attr("y1", y)
            .attr("y2", y)
            .attr("x1", -20)
            .attr("x2", width + margin.right);

		// reposition the legend
		legend.transition()
			.duration(0)
			.attr("transform", "translate(" + legendPosX + "," + legendPosY + ")");
	}
	

	function populateData(standings) {
		standings.forEach(function(team, index) {
			//console.log(team);
            
			chart.append("path")
					.attr("id","pathid-"+index)
					.attr("class", "line team"+index+" "+team.name)
					.attr("stroke", getColor(index))
					.style("stroke-width", 2.5)
				.on("mouseover", function() { emphasizeTeam(index); })			
				.on("mouseout", function() { deemphasizeTeam(index); })
				.append("svg:title")
					.text(team.name);


			chart.selectAll(".point").data(team.data)
				.enter().append("svg:circle")
					.attr("class","dot team"+index)
					.attr("stroke-width", 2)
					.attr("stroke", getColor(index))
					.attr("fill", getColor(index))
					.attr("cx", function(d) { return x(d.date) })
					.attr("cy", function(d) { return y(d.rank) })
					.attr("r", 3)
				.on("mouseover", function() { emphasizeTeam(index); })			
				.on("mouseout", function() { deemphasizeTeam(index); });
/*
			chart.selectAll(".pointtextbox").data(team.rank)
				.enter().append("svg:rect")
					.attr("class","dot textbox team"+index)
					.attr("stroke", color(index))
					.attr("fill", color(index))
					.attr("x", function(d) { return x(d.date) - 8})
					.attr("y", function(d) { return y(d.rank) - 26 })
					.attr("height", 16 )
					.attr("width",  16 )
					.attr("rx", 2 )
					.attr("ry", 2 )
					.style("opacity", 0);

			chart.selectAll(".pointtext").data(team.rank)
				.enter().append("svg:text")
					.attr("class","dot text team"+index)
					.attr("fill", "#222")
					// .attr("x", function(d) { return x(d.name) })
					// .attr("y", function(d) { return y(d[yMode]) - 14 })
					// .text(function(d) { return d[yMode] })
					.style("text-anchor", "middle")
					.style("opacity", 0);
*/
		});
		updateData(standings, 0);
	}
	
	function getData(team) {
		//console.log(team);
		data = team.map(function(team) {
			return {x:team['date'], y:team['rank']};;
		});
		//console.log(data);
		return data;
	}

	function updateData(standings, delay) {
		//console.log(standings);
        //x.domain(standings[0].rank.map(function(r) {return r.date;}));
		//y.domain([0, standings.length]);

		chart.selectAll(".line").transition()
			.duration(delay)
			.attr("d", function() {
				//console.log(".line",this.id);
				// console.log(parseInt(this.id.replace("pathid-","")));
            	return valueline(getData(standings[parseInt(this.id.replace("pathid-",""))].data));
			});

		chart.selectAll("circle.dot").transition()
			.duration(delay)
			.attr("cx", function(d) { return x(d.date); })
			.attr("cy", function(d) { return y(d.rank); });

		chart.selectAll(".dot.textbox").transition()
			.duration(delay)
			.attr("x", function(d) { return x(d.date) - 8; })
			.attr("y", function(d) { return y(d.rank) - 26; });

		chart.selectAll(".dot.text").transition()
			.duration(delay)
			.attr("x", function(d) { return x(d.date); })
			.attr("y", function(d) { return y(d.rank) - 14; })
			.text(function(d) { return d.rank; });

		chart.select(".y.axis").transition()
			.duration(delay)
			.call(yAxis)
				.selectAll("text")
					.attr("dy", ".35em")
					.style("font-size", "14px")
					.style("fill", "#000");
		
		chart.select(".y.axis").selectAll("line")
			.style("stroke", 'transparent');
        
        chart.select(".y.axis").selectAll("line.minor")
			.style("stroke", gridColor);
			
		
	}

	function buildAxis() {
		/*
        chart.append("g")
			.attr("class", "x grid")
			.attr("transform", "translate(0," + height + ")")
			.call(xGrid)
				.selectAll("line")
					.style("stroke", gridColor)
					.style("stroke-dasharray", (gridStroke));

		chart.append("g")
			.attr("class", "y grid")
			.call(yGrid)
				.selectAll("line")
					.style("stroke", gridColor)
					.style("stroke-dasharray", (gridStroke));
		*/
        
		// Add the X Axis
		var xaxisg = chart.append("g")
			.attr("class", "x axis")
			.attr("transform", "translate(0," + (height) + ")");
		
		xaxisg.call(xAxis)
				.selectAll("text")
					.style("text-anchor", "end")
					.style("fill", "#000")
					.style("font-size", "14px")
					//.style("font-weight", "bold")
//					.attr("dx", "-2em")
					.attr("dy", "1.5em")
					//.attr("transform", function() {	return "rotate(-90)" })
					.text(function(d,i) { return (i+1); });
        
		xaxisg.append("text")
			.attr("x", width/2)
			.attr("y", margin.bottom-18)
			.style("fill", "#000")
			.style("font-size", "14px")
			//.style("font-weight", "bold")
			.style("text-anchor", "middle")
			.text("Spieltag");
        
		// Add the Y Axis
		var yaxisg = chart.append("g")
			.attr("class", "y axis")
			.attr("transform", "translate(-10,0)")
			.call(yAxis);
        
        yaxisg.selectAll("line").data(y.ticks(standings.length*2), function(d) { return d; })
            .enter()
            .append("line")
            .attr("class", "minor")
            .attr("y1", y)
            .attr("y2", y)
            .attr("x1", -20)
            .attr("x2", width + margin.right);
			
		yaxisg.append("text")
			.attr("x", -(height/2))
			.attr("y", -margin.left/2)
			.style("text-anchor", "middle")
			.text("Platzierung")
				.attr("transform", function() {	return "rotate(-90)" });
	
	}

	function buildLegend(standings) {
		
		legend = chartsvg.append("g")
			.attr("class", "legend")
			.attr("transform", "translate(" + legendPosX + "," + legendPosY + ")");
	
        
        var size = standings.length;
		var positions = [];
        while(size--) positions[size] = 0;
        var size2 = standings.length;
		var positionShow = [];
        while(size2--) positionShow[size2] = 0;
		
		legendElement = legend.selectAll("g")
				.data(standings)
			.enter().append("g")
				.attr("transform", function(d, i) {
					var offset = function(p) {
						//console.log(standings[p].data[standings[i].rank.length-1].rank);
						var pos = standings[p].data[standings[p].data.length-1].rank -1;
						positions[pos] += 1;
						//console.log('offset ' + p + ' ' + positions[pos]);
						//console.log(pos);
						return (pos + 0.25 + (positions[pos])*0.5);
					};
					//console.log('trans  ' + i + ' ' + positions[i]);
					//console.log(standings[i].data[standings[i].rank.length-1].rank);
            		return "translate(" + 0 + "," + y(offset(i)) + ")";
				})
				.attr("class", function(d, i) {
            		var offset = function(p) {
						var pos = standings[p].data[standings[p].data.length-1].rank -1;
						//console.log(p);
						positionShow[pos] += 1;
						//console.log('class' + i + ' ' + positionShow[i]);
						return positionShow[pos];
					}
					if (offset(i) > 1) {
						return "noNr";
					}
					return;
				});
		//console.log(positions);
        
//		legendElement.append("rect")
//			.attr("class",function(d,i) { return "legendBox team"+i; } )
//			.attr("width", 10)
//			.attr("height", 10)
//			.style("fill", function(d,i) { 
//            		//return color(i); 
//            		return getColor(i);
//        		});
	
		legendElement.append("text")
			.attr("class",function(d,i) { return "legendNr team"+i; } )
			.attr("x", 10)
			.attr("y", 6)
			.attr("dy", ".35em")
			.style("font-size", "14px")
			.style("font-weight", "bold")
			.style("fill", function (d,i) {
					//console.log(this.parentNode.className.baseVal);
					if (this.parentNode.className.baseVal == 'noNr') {
						//console.log(pos);
						return "none";
					}
					return "black";
				})
			.style("cursor", "default")
			.style("text-anchor", "end")
			.text(function(d,i) {
				//console.log(d.data[d.data.length-1]);
				return d.data[d.data.length-1].rank;
            	//return "team"+i;
			});
		
		legendElement.append("text")
			.attr("class",function(d,i) { return "legendText team"+i; } )
			.attr("x", 18)
			.attr("y", 6)
			.attr("dy", ".35em")
			.style("font-size", "12px")
			.style("fill", "black")
			.style("cursor", "default")
			.style("text-anchor", "start")
			.text(function(d,i) {
				return d.name;
            	//return "team"+i;
			})
			.on('mouseover', function(d,i){ emphasizeTeam(i); })
			.on('mouseout', function(d,i){ deemphasizeTeam(i); });
	
		legendElement.append("text")
			.attr("class",function(d,i) { return "legendPoints team"+i; } )
			.attr("x", widthLegend-50)
			.attr("y", 6)
			.attr("dy", ".35em")
			.style("font-size", "12px")
			.style("fill", "black")
			.style("cursor", "default")
			.style("text-anchor", "start")
			.text(function(d,i) {
				//console.log(d);
				var p = d.data[d.data.length-1].points;
				var n = d.data[d.data.length-1].negpoints
				return p + ':' + n;
            	//return "team"+i;
			});
	
	}

	function getColor (i) {
     	var c=d3.rgb("#c9c9c9") // d3_Rgb object
        
		//TODO HKOG --> dynamic
		//if (standings[i].name.indexOf("HK Ostd/Geisl") > -1 ) {
        if (home == i ) {
            return blue;
        } else {
            //return "#ccc";
            return c.darker(0.15*i).toString(); 
        }   
    }
	
	function getColorEmf (i) {
     	if (home == i ) {
            return blue;
        } else {
            return yellow; 
        }   
    }
	
	d3.selection.prototype.moveToFront = function() {
		// console.log("move '" + this[0][0].textContent + "' to front");
		return this.each(function(){
			this.parentNode.appendChild(this);
		});
	};

	d3.selection.prototype.moveToBack = function() { 
		return this.each(function() { 
			var firstChild = this.parentNode.firstChild; 
			if (firstChild) { 
				this.parentNode.insertBefore(this, firstChild); 
			} 
		}); 
	};
	
	function emphasizeTeam(team) {
		//console.log(team);
		
		d3.selectAll(".line.team"+team).transition()
			.duration(emphasizeDelay)
			.style("stroke-width", 5)
			.attr("stroke", getColorEmf(team))
			.attr("d", function() {
            	return valueline(getData(standings[parseInt(this.id.replace("pathid-",""))].data));
			});;
		d3.selectAll(".dot.team"+team).transition()
			.duration(emphasizeDelay)
			.attr("stroke", getColorEmf(team))
			.attr("fill", getColorEmf(team))
			.attr("r", 5);
		d3.selectAll(".legendText.team"+team).transition()
			.duration(emphasizeDelay)
			.style("font-weight", 'bold');
		d3.selectAll(".legendBox.team"+team).transition()
			.duration(emphasizeDelay)
			.attr("width", 10)
			.attr("height", 10)
			.attr("transform", "translate(-1,-1)");
		d3.selectAll(".dot.textbox.team"+team).transition()
			.style("opacity", 0.7)
			.duration(2 * emphasizeDelay);
		d3.selectAll(".dot.text.team"+team).transition()
			.style("opacity", 0.9)
			.duration(3 * emphasizeDelay);

		d3.selectAll(".team"+team).moveToFront();
		d3.selectAll(".dot.text.team"+team).moveToFront();
	}

	function deemphasizeTeam(team) {
		d3.selectAll(".line.team"+team).transition()
			.duration(emphasizeDelay)
			.attr("stroke", getColor(team))
			.style("stroke-width", 2.5);
		d3.selectAll(".dot.team"+team).transition()
			.duration(emphasizeDelay)
			.attr("stroke", getColor(team))
			.attr("fill", getColor(team))
			.attr("r", 3);
		d3.selectAll(".legendText.team"+team).transition()
			.duration(emphasizeDelay)
			.style("font-weight", 'normal');
		d3.selectAll(".legendBox.team"+team).transition()
			.duration(emphasizeDelay)
			.attr("width", 8)
			.attr("height", 8)
			.attr("transform", "translate(0,0)");
		d3.selectAll(".dot.textbox.team"+team).transition()
			.style("opacity", 0)
			.duration(emphasizeDelay);
		d3.selectAll(".dot.text.team"+team).transition()
			.style("opacity", 0)
			.duration(emphasizeDelay);
		
		d3.selectAll(".dot.textbox,.dot.text").moveToBack();
		emphasizeTeam(home);
	}

});