jQuery(document).ready(function($){
	
	
//	$(".gamebutton").click(function(){
//		//console.log(this.id);
//		var gameId = this.id;
//		var table = document.getElementById("scorerTable");
//		
//		var season = table.getAttribute('data-season');
//		var teamkey = table.getAttribute('data-teamkey');
//		
//		$.ajax({
//			url:'index.php?option=com_hbteamhome&task=getGoals&format=raw',
//			type:'POST',
//			data: { gameId: gameId, season: season, teamkey: teamkey }, 
//			success:function(data){
//				//console.log(data);
//				table.innerHTML = data;
//			},
//			error:function(xhr,err){
//				// code for error
//				console.log(document.URL);
//				console.log("readyState: "+xhr.readyState+"\nstatus: "+xhr.status);
//				console.log("responseText: "+xhr.responseText);
//			}
//		});
//		
//	});
	
//	$.ajax({
//		url:'index.php?option=com_hbteamhome&task=getGoals4Chart',
//		type:'POST',
//		data: { season: season, teamkey: teamkey }, 
//		success:function(data){
//			//console.log(data);
//			table.innerHTML = data;
//		},
//		error:function(xhr,err){
//			// code for error
//			console.log(document.URL);
//			console.log("readyState: "+xhr.readyState+"\nstatus: "+xhr.status);
//			console.log("responseText: "+xhr.responseText);
//		}
//	});
	

	
	var margin = {top: 20, right: 150, bottom: 120, left: 50},
		width = 600 - margin.left - margin.right,
		height = 300 - margin.top - margin.bottom;

	var x = d3.scale.ordinal()
		.rangePoints([0, width]);

	var y = d3.scale.linear()
		.range([height, 0]);

	var xAxis = d3.svg.axis()
		.scale(x)
		.orient("bottom");

	var yAxis = d3.svg.axis()
		.scale(y)
		.orient("left");
	
	function make_x_axis(x) {
		return d3.svg.axis()
			.scale(x)
			 .orient("bottom")
			 .ticks(5);
	}

	function make_y_axis(y) {
		return d3.svg.axis()
			.scale(y)
			.orient("left")
			.ticks(10);
	}
	
	var color = d3.scale.category20c();
	
//	// Define the line
//	var	valueline = d3.svg.line()								// set 'valueline' to be a line
//	.x(function(d) { return x(d.game); })					// set the x coordinates for valueline to be the d.date values
//	.y(function(d) { return y(d.luis_herre); });					// set the y coordinates for valueline to be the d.close values

	var svg = d3.select("#chartgoals").append("svg")
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
	  .append("g")											// Append 'g' to the html 'body' of the web page
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")"); // in a place that is the actual area for the graph

	d3.json("index.php?option=com_hbteamhome&task=getGoals4Chart&format=raw", function(error, data) {
		//console.log(data);
		
		var keys = [];
		for (var key in data){
			if (data.hasOwnProperty(key) && key !== 'game') {
				keys.push(key);
			}
		}
		color.domain(keys);
		//console.log(keys);
		
		var max = [];
		keys.forEach(function(k) {
			var arr = data[k];
			max.push( Math.max.apply(null, arr.map(function(item){
				return item["y"];
			})));
		});
		//console.log(max);
		//console.log(d3.max(max));
		
		
		x.domain(data.game);
		y.domain([0, d3.max(max)]);
		// hardcoded for now, use d3.max(data, function(d) { return Math.max(d.alpha.yvalue, d.beta.yvalue, ...); })
		
		var legend = svg.append("g")
			.attr("class", "legend")
			.attr("width", 50)
			.attr("height", 50)
		  .selectAll("g")
			.data(color.domain().slice().reverse())
		  .enter().append("g")
			.attr("transform", function(d, i) { return "translate(" + (width + 20) + "," + i * 15 + ")"; });

		legend.append("rect")
			.attr("width", 8)
			.attr("height", 8)
			.style("fill", color);

		legend.append("text")
			.attr("x", 14)
			.attr("y", 9)
			.attr("dy", "-.25em")
			.text(function(d) { return d; });
		
		
		var valueline = d3.svg.line()
			//.defined(function(d) { return (d[k] !== null); })
			.x(function(d) { return x(d.x); })
			.y(function(d) { return y(d.y); });
	
		svg.append("g")
			.attr("class", "grid")
			.attr("transform", "translate(0," + height + ")")
			.call(make_x_axis(x)
				.tickSize(-height, 0, 0)
				.tickFormat("")
			)

		svg.append("g")
			.attr("class", "grid")
			.call(make_y_axis(y)
				.tickSize(-width, 0, 0)
				.tickFormat("")
			)
		
		// Add the X Axis
		svg.append("g")											// append the x axis to the 'g' (grouping) element
			.attr("class", "x axis")							// apply the 'axis' CSS styles to this path
			.attr("transform", "translate(0," + (height) + ")")	// move the drawing point to 0,height
			.call(xAxis)										// call the xAxis function to draw the axis
				.selectAll("text")  
				.style("text-anchor", "end")
				.attr("dx", "-0.8em")
				.attr("dy", "-0.5em")
				.attr("transform", function(d) {
					return "rotate(-90)" 
					});


		// Add the Y Axis
		svg.append("g")											// append the y axis to the 'g' (grouping) element
			.attr("class", "y axis")							// apply the 'axis' CSS styles to this path
			.attr("transform", "translate(0,0)")	// move the drawing point to 0,height
			.call(yAxis);										// call the yAxis function to draw the axis
	
		
		var i = 0;
		keys.forEach(function(k) {
			// Add the valueline path.
			svg.append("path")										// append the valueline line to the 'path' element
				.attr("class", "line")								// apply the 'line' CSS styles to this path
				.attr("d", valueline(data[k]))						// call the 'valueline' finction to draw the line
				.attr("stroke", color(k));
			//console.log(data[k]);
			
			svg.selectAll(".point").data(data[k])
			  .enter().append("svg:circle")
				 .attr("stroke-width", 2)
				 .attr("stroke", color(k))
				 .attr("fill", color(k))
				 .attr("cx", function(d, i) { return x(d.x) })
				 .attr("cy", function(d, i) { return y(d.y) })
				 .attr("r", function(d, i) { return 2 });
			
			i++;
		});
		
	
		
		


	});
	
	var margin2 = {top: 20, right: 150, bottom: 120, left: 50},
		width2 = 600 - margin2.left - margin2.right,
		height2 = 350 - margin2.top - margin2.bottom;

	var x2 = d3.scale.ordinal()
		.rangePoints([0, width2]);

	var y2 = d3.scale.linear()
		.range([height2, 0]);

	var xAxis2 = d3.svg.axis()
		.scale(x2)
		.orient("bottom");

	var yAxis2= d3.svg.axis()
		.scale(y2)
		.orient("left");
	
	
	var color = d3.scale.category20c();

	var svg2 = d3.select("#chartgoalstotal").append("svg")
		.attr("width", width2 + margin2.left + margin2.right)
		.attr("height", height2 + margin2.top + margin2.bottom)
	  .append("g")											// Append 'g' to the html 'body' of the web page
		.attr("transform", "translate(" + margin2.left + "," + margin2.top + ")"); // in a place that is the actual area for the graph

	d3.json("index.php?option=com_hbteamhome&task=getGoals4Chart&format=raw", function(error, data) {
		//console.log(data);
		
		var keys = [];
		for (var key in data){
			if (data.hasOwnProperty(key) && key !== 'game') {
				keys.push(key);
			}
		}
		color.domain(keys);
		//console.log(keys);
		
		var max = [];
		keys.forEach(function(k) {
			var arr = data[k];
			max.push( Math.max.apply(null, arr.map(function(item){
				return item["y2"];
			})));
		});
		//console.log(max);
		//console.log(d3.max(max));
		
		
		x2.domain(data.game);
		y2.domain([0, Math.ceil(d3.max(max) / 10) * 10]);
		// hardcoded for now, use d3.max(data, function(d) { return Math.max(d.alpha.yvalue, d.beta.yvalue, ...); })
		
		svg2.append("g")
			.attr("class", "grid")
			.attr("transform", "translate(0," + height2 + ")")
			.call(make_x_axis(x2)
				.tickSize(-height2, 0, 0)
				.tickFormat("")
			)

		svg2.append("g")
			.attr("class", "grid")
			.call(make_y_axis(y2)
				.tickSize(-width2, 0, 0)
				.tickFormat("")
			)
		
		var legend = svg2.append("g")
			.attr("class", "legend")
			.attr("width", 50)
			.attr("height", 50)
		  .selectAll("g")
			.data(color.domain().slice().reverse())
		  .enter().append("g")
			.attr("transform", function(d, i) { return "translate(" + (width + 20) + "," + i * 15 + ")"; });

		legend.append("rect")
			.attr("width", 8)
			.attr("height", 8)
			.style("fill", color);

		legend.append("text")
			.attr("x", 14)
			.attr("y", 9)
			.attr("dy", "-.25em")
			.text(function(d) { return d; });
		
		
		// Add the X Axis
		svg2.append("g")											// append the x axis to the 'g' (grouping) element
			.attr("class", "x axis")							// apply the 'axis' CSS styles to this path
			.attr("transform", "translate(0," + (height2) + ")")	// move the drawing point to 0,height
			.call(xAxis2)										// call the xAxis function to draw the axis
				.selectAll("text")  
				.style("text-anchor", "end")
				.attr("dx", "-0.8em")
				.attr("dy", "-0.5em")
				.attr("transform", function(d) {
					return "rotate(-90)" 
					});										// call the xAxis function to draw the axis

		// Add the Y Axis
		svg2.append("g")											// append the y axis to the 'g' (grouping) element
			.attr("class", "y axis")							// apply the 'axis' CSS styles to this path
			.attr("transform", "translate(0,0)")	// move the drawing point to 0,height
			.call(yAxis2);										// call the yAxis function to draw the axis
		
		var valueline = d3.svg.line()
			//.defined(function(d) { return (d[k] !== null); })
			.x(function(d) { return x2(d.x); })
			.y(function(d) { return y2(d.y2); });
	
		
		 
		var i = 0;
		keys.forEach(function(k) {
			// Add the valueline path.
			svg2.append("path")										// append the valueline line to the 'path' element
				.attr("class", "line")								// apply the 'line' CSS styles to this path
				.attr("d", valueline(data[k]))						// call the 'valueline' finction to draw the line
				.attr("stroke", color(k));
			//console.log(data[k]);
			
			svg2.selectAll(".point").data(data[k])
			  .enter().append("svg:circle")
				 .attr("stroke-width", 2)
				 .attr("stroke", color(k))
				 .attr("fill", color(k))
				 .attr("cx", function(d, i) { return x2(d.x) })
				 .attr("cy", function(d, i) { return y2(d.y2) })
				 .attr("r", function(d, i) { return 2 });
			
			i++;
		});
		

	});
});