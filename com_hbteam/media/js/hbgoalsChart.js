jQuery(document).ready(function($){
	//console.log(teamkey);
	//console.log(season);
	//console.log(futureGames);

	// dimensions
	var margin = {top: 30, right: 150, bottom: 120, left: 50};
	var width = parseInt(d3.select('#chartgoals').style('width'), 10);
	var height = 400;
	width = width - margin.left - margin.right;
	height = height - margin.top - margin.bottom;
	//console.log(width);

	// time duration for changing dataset
	var yDelay = 500;
	var emphasizeDelay = 100;

	var x = d3.scale.ordinal()
		.rangePoints([0, width]);

	var y = d3.scale.linear()
		.range([height, 0]);

	var color = d3.scale.category20c();

	var gridColor = "#ddd";
	var gridStroke = "2, 2";

	var xAxis = d3.svg.axis()
		.scale(x)
		.orient("bottom");

	var yAxis = d3.svg.axis()
		.scale(y)
		.tickSize(-10, 0, 0)
		.orient("left");

	// using ticks as workaround for grid lines
	var xGrid = d3.svg.axis()
		.scale(x)
		.orient("bottom")
		.tickSize(-height, 0, 0)
		.tickFormat("")
		.ticks(10);

	// using ticks as workaround for grid lines
	var yGrid = d3.svg.axis()
		.scale(y)
		.orient("left")
		.tickSize(-width, 0, 0)
		.tickFormat("")
		.ticks(10);

	// variable as switch for what data should be displayed
	var yMode = getYMode();

	// line, that is bound to game data
	var valueline = d3.svg.line()
		.x(function(d) { return x(d.x); })
		.y(function(d) { return y(d.y); });

	// create the svg for chart
	var chartsvg = d3.select("#chartgoals").append("svg")
			.attr("width", width + margin.left + margin.right)
			.attr("height", height + margin.top + margin.bottom)
			.style("fill", "transparent");

	// svg background
	var chartBackground = chartsvg.append("rect")
		.attr("width", "100%")
		.attr("height", "100%");

	// Append 'g' in a place that is the actual area for the graph
	var chart = chartsvg.append("g")
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	// group for legend
	var legend;

	// url to get the chart data
	var url = "index.php?option=com_hbteam&task=getGoals4Chart&format=raw"
			+ "&teamkey=" + teamkey + "&season=" + season + "&futureGames=" + futureGames;
	//console.log(url);

	d3.json(url, function(error, data) {
		//console.log(data);
		x.domain(data.games.map(function(game) {return game.name;}));
		y.domain([0, getMaxY(data.players)]);

		// draw chart
		buildAxis();
		buildLegend(data.players);
		populateData(data);

		// trigger to change the displayed dataset
		// d3.selectAll("[name=mode]").on("change", function() {
		d3.select("#hbgoalchart_chartmode").selectAll('input').on("change", function() {
			yMode = this.value;
			updateData(data, yDelay);
		});

		// resizing for responsive chart
		d3.select(window).on('resize', function() { resize(data); });
	});

	function resize(data) {

		// update width
		var width = parseInt(d3.select('#chartgoals').style('width'), 10);
		width = width - margin.left - margin.right;
		// console.log(width);
	
		x.rangePoints([0, width]);
		// y.range([height, 0]);

		// resize the chart
		chartsvg
			.attr('height', (height + margin.top + margin.bottom) + 'px')
			.attr('width', (width + margin.left + margin.right) + 'px');

		// change the line
		updateData(data, 0);

		// change the x axis
		chart.select(".x.axis")
			.call(xAxis)
				.selectAll("text")
				.style("text-anchor", "end")
				.attr("dx", "-1em")
				.attr("dy", "-0.5em");

		chart.select(".x.grid")
			.call(xGrid);

		chart.select(".y.grid").transition()
			.duration(0)
			.call(yGrid.tickSize(-width, 0, 0));

		// reposition the legend
		legend.transition()
			.duration(0)
			.attr("transform", function(d, i) {
				return "translate(" + (width + margin.left + 20) + "," + (margin.top + i * 16) + ")";
			});
	}

	function populateData(data) {
		players = data.players;
		players.forEach(function(player, index) {

			chart.append("path")
					.attr("id","pathid-"+index)
					.attr("class", "line player"+index+" "+player.alias)
					// .attr("d", valueline(getData(player.data, yMode)))
					.attr("stroke", color(index))
					.style("stroke-width", 2.5)
				.on("mouseover", function() { emphasizePlayer(index); })			
				.on("mouseout", function() { deemphasizePlayer(index);	})
				.append("svg:title")
					.text(player.name);


			chart.selectAll(".point").data(player.data)
				.enter().append("svg:circle")
					.attr("class","dot player"+index)
					.attr("stroke-width", 2)
					.attr("stroke", color(index))
					.attr("fill", color(index))
					// .attr("cx", function(d) { return x(d.name) })
					// .attr("cy", function(d) { return y(d[yMode]) })
					.attr("r", 3)
				.on("mouseover", function(d) { emphasizePlayer(index); })			
				.on("mouseout", function(d) { deemphasizePlayer(index);	});

			chart.selectAll(".pointtextbox").data(player.data)
				.enter().append("svg:rect")
					.attr("class","dot textbox player"+index)
					.attr("stroke", color(index))
					.attr("fill", color(index))
					// .attr("x", function(d) { return x(d.name) - 8})
					// .attr("y", function(d) { return y(d[yMode]) - 26 })
					.attr("height", 16 )
					.attr("width",  16 )
					.attr("rx", 2 )
					.attr("ry", 2 )
					.style("opacity", 0);

			chart.selectAll(".pointtext").data(player.data)
				.enter().append("svg:text")
					.attr("class","dot text player"+index)
					.attr("fill", "#222")
					// .attr("x", function(d) { return x(d.name) })
					// .attr("y", function(d) { return y(d[yMode]) - 14 })
					// .text(function(d) { return d[yMode] })
					.style("text-anchor", "middle")
					.style("opacity", 0);

		});
		updateData(data, 0);
	}

	function updateData(data, delay) {
		//console.log(data);

		x.domain(data.games.map(function(game) {return game.name;}));
		y.domain([0, getMaxY(data.players)]);

		chart.selectAll(".line").transition()
			.duration(delay)
			.attr("d", function() {
				// console.log(".line",this.id);
				// console.log(parseInt(this.id.replace("pathid-","")));
				return valueline(getData(data.players[parseInt(this.id.replace("pathid-",""))].data));
				//return valueline(getData(player.data, yMode));
			});

		chart.selectAll("circle.dot").transition()
			.duration(delay)
			.attr("cx", function(d) { return x(d.name); })
			.attr("cy", function(d) { return y(d[yMode]); });

		chart.selectAll(".dot.textbox").transition()
			.duration(delay)
			.attr("x", function(d) { return x(d.name) - 8; })
			.attr("y", function(d) { return y(d[yMode]) - 26; });

		chart.selectAll(".dot.text").transition()
			.duration(delay)
			.attr("x", function(d) { return x(d.name); })
			.attr("y", function(d) { return y(d[yMode]) - 14; })
			.text(function(d) { return d[yMode] });

		chart.select(".y.axis").transition()
			.duration(delay)
			.call(yAxis)
				.selectAll("text")
					.style("font-size", "10px")
					.style("fill", "#000");
		
		chart.select(".y.axis").selectAll("line")
			.style("stroke", gridColor)
			.style("stroke-dasharray", (gridStroke));

		chart.select(".y.grid").transition()
			.duration(delay)
			.call(yGrid)
				.selectAll("line")
					.style("stroke", gridColor)
					.style("stroke-dasharray", (gridStroke));
	}

	function emphasizePlayer(player) {
		// console.log(player);
		d3.select("#pathid-"+player).transition()
			.duration(emphasizeDelay)
			.style("stroke-width", 5);
		d3.selectAll(".dot.player"+player).transition()
			.duration(emphasizeDelay)
			.attr("r", 5);
		d3.selectAll(".legendText.player"+player).transition()
			.duration(emphasizeDelay)
			.style("font-size", "11px")
			.style("font-weight", 'bold');
		d3.selectAll(".legendBox.player"+player).transition()
			.duration(emphasizeDelay)
			.attr("width", 10)
			.attr("height", 10)
			.attr("transform", "translate(-1,-1)");
		d3.selectAll(".dot.textbox.player"+player).transition()
			.style("opacity", 0.7)
			.duration(2 * emphasizeDelay);
		d3.selectAll(".dot.text.player"+player).transition()
			.style("opacity", 0.9)
			.duration(3 * emphasizeDelay);

		d3.selectAll(".player"+player).moveToFront();
		d3.selectAll(".dot.text.player"+player).moveToFront();
	}

	function deemphasizePlayer(player) {
		d3.select("#pathid-"+player).transition()
			.duration(emphasizeDelay)
			.style("stroke-width", 2.5);
		d3.selectAll(".dot.player"+player).transition()
			.duration(emphasizeDelay)
			.attr("r", 3);
		d3.selectAll(".legendText.player"+player).transition()
			.duration(emphasizeDelay)
			.style("font-size", "10px")
			.style("font-weight", 'normal');
		d3.selectAll(".legendBox.player"+player).transition()
			.duration(emphasizeDelay)
			.attr("width", 8)
			.attr("height", 8)
			.attr("transform", "translate(0,0)");
		d3.selectAll(".dot.textbox.player"+player).transition()
			.style("opacity", 0)
			.duration(emphasizeDelay);
		d3.selectAll(".dot.text.player"+player).transition()
			.style("opacity", 0)
			.duration(emphasizeDelay);
		
		d3.selectAll(".dot.textbox,.dot.text").moveToBack();
	}

	function getMaxY(players) {
		var maxY = d3.max(players.map(function(games) {
			//console.log(games.data);
			return d3.max(games.data.map(function(game) {
				//console.log(game.goals);
				return game[yMode];
			}));
		}));
		if (maxY < 10) maxY = 10;
		// console.log(maxY);		
		return maxY;
	}

	function buildAxis() {
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

		// Add the X Axis
		chart.append("g")
			.attr("class", "x axis")
			.attr("transform", "translate(0," + (height) + ")")
			.call(xAxis)
				.selectAll("text")
					.style("text-anchor", "end")
					.style("fill", "#000")
					.style("font-size", "10px")
					.attr("dx", "-1em")
					.attr("dy", "-0.5em")
					.attr("transform", function() {	return "rotate(-90)" });

		// Add the Y Axis
		chart.append("g")
			.attr("class", "y axis")
			.attr("transform", "translate(-10,0)")
			.call(yAxis);
	}

	function buildLegend(players) {
		
		legend = chartsvg.append("g")
			.attr("class", "legend")
			.selectAll("g")
				.data(players)
			.enter().append("g")
				.attr("transform", function(d, i) {
					return "translate(" + (width + margin.left + 20) + "," + (margin.top + i * 16) + ")";
				});

		legend.append("rect")
			.attr("class",function(d,i) { return "legendBox player"+i; } )
			.attr("width", 8)
			.attr("height", 8)
			.style("fill", function(d,i) { return color(i); });

		legend.append("text")
			.attr("class",function(d,i) { return "legendText player"+i; } )
			.attr("x", 14)
			.attr("y", 4.5)
			.attr("dy", ".35em")
			.style("font-size", "10px")
			.style("fill", "black")
			.style("cursor", "default")
			.style("text-anchor", "start")
			.text(function(d) {
				return d.name;
			})
			.on('mouseover', function(d,i){ emphasizePlayer(i); })
			.on('mouseout', function(d,i){ deemphasizePlayer(i); });
	}


	function getData(player) {
		data = player.map(function(game) {
			return {x:game['name'], y:game[yMode]};;
		})
		//console.log(data);
		return data;
	}

	function getYMode() {
		// return d3.selectAll("[name=mode]")
		return d3.select("#hbgoalchart_chartmode").selectAll('input')
			.filter(function() {
				return this.checked == true;
			})[0][0].value;
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

});