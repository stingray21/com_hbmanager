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
//			url:'index.php?option=com_hbteam&task=getGoals&format=raw',
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
//		url:'index.php?option=com_hbteam&task=getGoals4Chart',
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
	

	
	d3.selection.prototype.moveToFront = function() {
	  return this.each(function(){
		this.parentNode.appendChild(this);
	  });
	};

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

	var div = d3.select("#chartgoals").append("div")   
		.attr("class", "d3tooltip")               
		.style("opacity", 0);
	
	var svg = d3.select("#chartgoals").append("svg")
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
	.append("g")											// Append 'g' to the html 'body' of the web page
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")"); // in a place that is the actual area for the graph

		
	var valueline = d3.svg.line()
		//.defined(function(d) { return (d[k] !== null); })
		.x(function(d) { return x(d.game); })
		.y(function(d) { return y(d.goals); });

	d3.json("index.php?option=com_hbteam&task=getGoals4Chart&format=raw", function(error, data) {
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
				return item["goals"];
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
			.text(function(d) { 
				var goalie = '';
				if (data[d][0].goalie === "TW") goalie = " (TW)";
				return data[d][0].name + goalie ; })
			.on('mouseover', function(d){
				d3.select("#pathid-"+d).transition()
				  .duration(100)
				  .style("stroke-width", 3); 
				d3.selectAll(".dot."+d).transition()
				  .duration(100)
				  .attr("r", 4);
				d3.selectAll(".dottextbox."+d).transition()
				  .duration(0)
				  .attr("transform", "translate(0," + height + ")")
				  .each("end",function() { 
					  d3.select(this).transition()
						.style("opacity", 0.9)
						.duration(200);
					});
				d3.selectAll(".dottext."+d).transition()
				  .duration(0)
				  .attr("transform", "translate(0," + height + ")")
				  .each("end",function() { 
					  d3.select(this).transition()
						.style("opacity", 0.9)
						.duration(200);
					});
				var sel = d3.selectAll("."+d);
				sel.moveToFront();
			})
			.on('mouseout', function(d){
				d3.select("#pathid-"+d).transition()
				  .duration(100)
				  .style("stroke-width", 1.5); 
				d3.selectAll(".dot."+d).transition()
				  .duration(100)
				  .attr("r", 2);
				d3.selectAll(".dottextbox."+d).transition()
				  .duration(200)
				  .style("opacity", 0)
				  .each("end",function() { 
					  d3.select(this).transition()
						.attr("transform", "translate(0,-" + height + ")")
						.duration(0);
					});
				d3.selectAll(".dottext."+d).transition()
				  .duration(200)
				  .style("opacity", 0)
				  .each("end",function() { 
					  d3.select(this).transition()
						.attr("transform", "translate(0,-" + height + ")")
						.duration(0);
					});
			});

		svg.append("g")
			.attr("class", "grid")
			.attr("transform", "translate(0," + height + ")")
			.call(make_x_axis(x)
				.tickSize(-height, 0, 0)
				.tickFormat("")
			);

		svg.append("g")
			.attr("class", "grid")
			.call(make_y_axis(y)
				.tickSize(-width, 0, 0)
				.tickFormat("")
			);
		
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
			svg.append("path")
		.attr("id","pathid-"+k)										// append the valueline line to the 'path' element
				.attr("class", "line "+k)								// apply the 'line' CSS styles to this path
				.attr("d", valueline(data[k]))						// call the 'valueline' finction to draw the line
				.attr("stroke", color(k))
				.append("svg:title")
		.text(k);
			//console.log(data[k]);
			
			svg.selectAll(".point").data(data[k])
				.enter().append("svg:circle")
				  .attr("class","dot "+k)
				  .attr("stroke-width", 2)
				  .attr("stroke", color(k))
				  .attr("fill", color(k))
				  .attr("cx", function(d, i) { return x(d.game) })
				  .attr("cy", function(d, i) { return y(d.goals) })
				  .attr("r", function(d, i) { return 2 })
				  .on("mouseover", function(d) {  
					//console.log(d);    
					div.transition()        
						.duration(200)      
						.style("opacity", 0.9);      
					div.html(function() { 
						  //console.log(d);
						  var tttext = '';
						  tttext += "<b>" + d.name + "</b>";
						  if (d.goalie === "TW") tttext += " (TW)";
						  tttext += "<br/>" + d.goals;
						  if (d.penalty !== null) tttext += "/" + d.penalty;
						  if (d.goals == 1) tttext += " Tor";
						  else tttext += " Tore gegen";
						  tttext += "<br/>" + d.game;
						  return tttext ; } )  
						.style("opacity", 1) 
						.style("left", function() { return (x(d.game)+margin.left) + "px";})     
						.style("top", function() { return (y(d.goals)+margin.top-57) + "px";});   
					d3.select("#pathid-"+k).transition()
					  .duration(100)
					  .style("stroke-width", 3); 
					d3.selectAll(".dot."+k).transition()
					  .duration(100)
					  .attr("r", 4);

					var sel = d3.selectAll("."+k);
					sel.moveToFront();
					})                  
				.on("mouseout", function(d) {       
					div.transition()        
						.duration(500)      
						.style("opacity", 0);
					d3.select("#pathid-"+k).transition()
					  .duration(100)
					  .style("stroke-width", 1.5); 
					d3.selectAll(".dot."+k).transition()
					  .duration(100)
					  .attr("r", 2);  
					});

			  svg.selectAll(".pointtextbox").data(data[k])
				.enter().append("svg:rect")
				  .attr("class","dottextbox "+k)
				  .attr("stroke", color(k))
				  .attr("fill", color(k))
				  .attr("x", function(d, i) { return x(d.game) - 7})
				  .attr("y", function(d, i) { return y(d.goals) - 21 - height })
				  .attr("height", 14 )
				  .attr("width",  14 )
				  .attr("rx", 2 )
				  .attr("ry", 2 )
				  .style("opacity", 0) 
				  ;

			  svg.selectAll(".pointtext").data(data[k])
				.enter().append("svg:text")
				  .attr("class","dottext "+k)
				  .attr("fill", "#222")
				  .attr("x", function(d, i) { return x(d.game) })
				  .attr("y", function(d, i) { return y(d.goals) - 10 - height })
				  .text(function(d, i) { return d.goals })
				  .style("text-anchor", "middle") 
				  .style("opacity", 0) 
				  ;

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

	d3.json("index.php?option=com_hbteam&task=getGoals4Chart&format=raw", function(error, data) {
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
				return item["goalsTotal"];
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
			.x(function(d) { return x2(d.game); })
			.y(function(d) { return y2(d.goalsTotal); });
	
		
		 
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
				 .attr("cx", function(d, i) { return x2(d.game) })
				 .attr("cy", function(d, i) { return y2(d.goalsTotal) })
				 .attr("r", function(d, i) { return 2 });
			
			i++;
		});
		

	});
});