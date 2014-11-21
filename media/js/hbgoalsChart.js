jQuery(document).ready(function($){
	console.log(teamkey);
	console.log(season);
	
	d3.selection.prototype.moveToFront = function() {
	  return this.each(function(){
		this.parentNode.appendChild(this);
	  });
	};

	var margin = {top: 20, right: 150, bottom: 120, left: 50},
		width = 550 - margin.left - margin.right,
		height = 400 - margin.top - margin.bottom;

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

	var xGrid = d3.svg.axis()
		.scale(x)
		.orient("bottom")
		.tickSize(-height, 0, 0)
		.tickFormat("")
		.ticks(10);
	
	var yGrid = d3.svg.axis()
		.scale(y)
		.orient("left")
		.tickSize(-width, 0, 0)
		.tickFormat("")
		.ticks(10);
	

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
	
	var valuelineTotal = d3.svg.line()
		//.defined(function(d) { return (d[k] !== null); })
		.x(function(d) { return x(d.game); })
		.y(function(d) { return y(d.goalsTotal); });
	
	var url = "index.php?option=com_hbteam&task=getGoals4Chart&format=raw" 
			+ "&teamkey=" + teamkey + "&season=" + season;
	//console.log(url);
	d3.json(url, function(error, data) {
		//console.log(data);
		
		var keys = [];
		for (var key in data){
			if (data.hasOwnProperty(key) && key !== 'game') {
				keys.push(key);
			}
		}
		color.domain(keys);
		//console.log(keys);
		
		
		function getMax(y) {
			var max = [];
			keys.forEach(function(k) {
				var arr = data[k];
				max.push( Math.max.apply(null, arr.map(function(item){
					return item[y];
				})));
			});
			return d3.max(max);
		}
		var maxSingle = getMax("goals"); 
		//console.log(maxSingle);
		var maxTotal = getMax("goalsTotal"); 
		
		
		
		x.domain(data.game);
		y.domain([0, maxSingle]);
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
				d3.selectAll(".dot.textbox."+d).transition()
				  .duration(0)
				  .attr("transform", "translate(0," + height + ")")
				  .each("end",function() { 
					  d3.select(this).transition()
						.style("opacity", 0.9)
						.duration(200);
					});
				d3.selectAll(".dot.text."+d).transition()
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
				d3.selectAll(".dot.textbox."+d).transition()
				  .duration(200)
				  .style("opacity", 0)
				  .each("end",function() { 
					  d3.select(this).transition()
						.attr("transform", "translate(0,-" + height + ")")
						.duration(0);
					});
				d3.selectAll(".dot.text."+d).transition()
				  .duration(200)
				  .style("opacity", 0)
				  .each("end",function() { 
					  d3.select(this).transition()
						.attr("transform", "translate(0,-" + height + ")")
						.duration(0);
					});
			});

		svg.append("g")
			.attr("class", "x grid")
			.attr("transform", "translate(0," + height + ")")
			.call(xGrid);

		svg.append("g")
			.attr("class", "y grid")
			.call(yGrid);
		
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
			.attr("transform", "translate(0,0)")				// move the drawing point to 0,height
			.call(yAxis);										// call the yAxis function to draw the axis
	
		
		var i = 0;
		keys.forEach(function(k) {
			// Add the valueline path.
			//console.log("data to path",data[k]);
			
			svg.append("path")
				//.data(data[k])
				.attr("id","pathid-"+k)							// append the valueline line to the 'path' element
				.attr("class", "line "+k)						// apply the 'line' CSS styles to this path
				.attr("d", valueline(data[k]))					// call the 'valueline' finction to draw the line
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
						  if (d.goals == 1) tttext += " Tor gegen";
						  else tttext += " Tore gegen";
						  tttext += "<br/>" + d.game;
						  return tttext ; } )  
						.style("opacity", 1) 
						.style("left", function() { return (x(d.game)+margin.left) + "px";})    
						.style("top", function() { 
							//console.log(d3.select("#mode-total").node().checked);	
							//console.log(document.getElementById('mode-total').checked);
							var yGoals = d.goals;
							if (d3.select("#mode-total").node().checked === true) {
								yGoals = d.goalsTotal;
							}								
							ypx = (y(yGoals)+margin.top-57) + "px";
							return ypx;});   
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
				  .attr("class","dot textbox "+k)
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
				  .attr("class","dot text "+k)
				  .attr("fill", "#222")
				  .attr("x", function(d, i) { return x(d.game) })
				  .attr("y", function(d, i) { return y(d.goals) - 10 - height })
				  .text(function(d, i) { return d.goals })
				  .style("text-anchor", "middle") 
				  .style("opacity", 0) 
				  ;

			i++;
		});
		
		
		
		
		d3.selectAll("input").on("change", change);


		function change() {
		  if (this.value === "single") transitionSingle();
		  else transitionTotal();
		}	

		function transitionSingle() {
			y.domain([0, maxSingle]);
			curNameCirc = '';
			iPlusCirc = 0;
			svg.selectAll("circle.dot").transition()
				.duration(function(d,i) {
					if (curNameCirc !== d.name) {
						curNameCirc = d.name;
						iPlusCirc += 200;
					}
					//console.log(".circle",(iPlusCirc), d.name);
					//return 500;
					return (iPlusCirc); 
				})
				.attr("cy", function(d) {
					//console.log(y(d.goalsTotal));
					return y(d.goals); 
				});
			svg.selectAll("rect.dot").transition()
				.duration(function(d,i) {
					return (200); 
				})
				.attr("y", function(d) {
					//console.log(y(d.goalsTotal));
					return y(d.goals) - 21 - height; 
				});
			svg.selectAll("text.dot").transition()
				.duration(function(d,i) {
					//console.log(".line",(500+i*100));
					return (200); 
				})
				.attr("y", function(d) {
					//console.log("text.dot",d);
					//console.log(y(d.goalsTotal));
					return y(d.goals) - 10 - height; 
				});
			
			//valueline.y(function(d) { return y(d.goalsTotal); });
			svg.selectAll(".line").transition()
				.duration(function(d,i) {
					//console.log(".line",(200+i*200));
					//return 500;
					return (200+i*200); 
				})
				.attr("d", function() {
					//console.log(".line",this.textContent);
					return valueline(data[this.textContent]); 
				});
			
			svg.selectAll(".y.axis").transition()
				.duration(500)
				//.attr("fill", "red")
				.call(yAxis);
			svg.selectAll(".y.grid").transition()
				.duration(500)
				//.attr("fill", "red")
				.call(yGrid);
		}

		function transitionTotal() {
			y.domain([0, Math.ceil(maxTotal / 10) * 10]);
			
			curNameCirc = '';
			iPlusCirc = 0;
			svg.selectAll("circle.dot").transition()
				.duration(function(d,i) {
					if (curNameCirc !== d.name) {
						curNameCirc = d.name;
						iPlusCirc += 200;
					}
					//console.log(".circle",(iPlusCirc), d.name);
					//return 500;
					return (iPlusCirc); 
				})
				.attr("cy", function(d) {
					//console.log(y(d.goalsTotal));
					return y(d.goalsTotal); 
				});
			svg.selectAll("rect.dot").transition()
				.duration(function(d,i) {
					//console.log(".line",(500+i*100));
					return (200); 
				})
				.attr("y", function(d) {
					//console.log(y(d.goalsTotal));
					return y(d.goalsTotal) - 21 - height; 
				});
			svg.selectAll("text.dot").transition()
				.duration(function(d,i) {
					//console.log(".line",(500+i*100));
					return (200); 
				})
				.attr("y", function(d) {
					//console.log("text.dot",d);
					//console.log(y(d.goalsTotal));
					return y(d.goalsTotal) - 10 - height; 
				});
			
			//valueline.y(function(d) { return y(d.goalsTotal); });
			svg.selectAll(".line").transition()
				.duration(function(d,i) {
					//console.log(".line",(200+i*200));
					//return 500;
					return (200+i*200); 
				})
				.attr("d", function() {
					//console.log(".line",this.textContent);
					return valuelineTotal(data[this.textContent]); 
				});
			
			svg.selectAll(".y.axis").transition()
				.duration(500)
				//.attr("fill", "red")
				.call(yAxis);
			svg.selectAll(".y.grid").transition()
				.duration(500)
				//.attr("fill", "red")
				.call(yGrid);
		}
	});
	
});