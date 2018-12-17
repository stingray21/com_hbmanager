document.addEventListener("DOMContentLoaded", function(event) {
	//console.log("DOM fully loaded and parsed");

console.log(teamkey, gameId, season);
// ============= definitions =================

// var url = "http://localhost/handball/hb_joomla3/hbdata/hkog.json";
var url = './index.php?option=com_hbmanager&task=getGameData&format=raw&teamkey=' + teamkey + '&gameId=' + gameId;
var gamesData;
d3.json(url, function(error, data) {
	if (error != null) { console.log(error) };
	// console.log(data);
	if (typeof data != 'undefined' || data.playerdata !== null) {
		gamesData = data;
		// console.log(gamesData);
		calcDimensions();
		initializeGraph();
		updateData(gameId, loadData);
	} else {
		console.log('No data');
	}
});


// dimensions

var margin = { top: 5, right: 20, bottom: 10, left: 20 };

var frameWidth 	= 800;
var frameHeight = 900;

var width, height;

var chartMargin = { top: 30, right: 70, bottom: 20, left: 220 };
var chartWidth, chartHeight;

var percent_left_col, 	width_left_col;
var percent_middle_col, width_middle_col;
var percent_right_col, 	width_right_col;
var percent_top_row, 	height_top_row;
var percent_bottom_row, height_bottom_row;

var numGoals = 10;
var yMin = 0;
var yMax = 3600;
var startTime = yMax;

// slider parameters
var sliderWidth,sliderHeight,sliderMin,sliderMax,sliderstart,sliderOffsetLeft,sliderOffsetTop,handleWidth;



// scoreboard
var sbheight = 100;
var sbwidth = 150;
var sbtopmargin = 5;
var sbscoreline = 88;
var sbTime = 37;
var sbHome = 53;
var sbAway = 135;
var sbradius = 8;
var sboffset = 2;


// url to sprite sheet file
// var players_sprite = 'http://localhost/handball/hb_joomla3/hbdata/playersprites.png';
var default_players_sprite = false;
var players_sprite = '../../../hbdata/spritesheets/'+teamkey+'_playersprites_'+season+'.png'; // works online
var playersImage = new Image();
playersImage.src = players_sprite;
playersImage.onerror = function() {
	// console.log(JSON.stringify(players_sprite) + ' not found');
	players_sprite = players_sprite.replace('../../', '');
	// playersImage = new Image();
	playersImage.src = players_sprite;
	playersImage.onerror = function() {
		// console.log(JSON.stringify(players_sprite) + ' not found');
		players_sprite = '../../../media/com_hbmanager/images/default_playersprites.png';
		default_players_sprite = true;
		// playersImage = new Image();
		playersImage.src = players_sprite;
		playersImage.onerror = function() {
			// console.log(JSON.stringify(players_sprite) + ' not found');
			players_sprite = players_sprite.replace('../../', '');
		}
	}
}

var picWidthFactor = 1;

// var players_sprite = '../hbdata/spritesheets/'+teamkey+'_playersprites_'+season+'.png'; // works local
// console.log(players_sprite);
// console.log(JSON.stringify(players_sprite));

// player profiles
var picRadius = 22;
var profilesOffset = { "left": 30, "top": margin.top };


function calcDimensions() {

	frameWidth = parseInt(d3.select('#gamegraphframe').style('width'), 10);
	// console.log(frameWidth);

	width = frameWidth - margin.left - margin.right;
	height = frameHeight - margin.top - margin.bottom;
	
	if (frameWidth < 500) {
		percent_left_col = 0;
	} else {
		percent_left_col = 30;
	}

	percent_middle_col = 25;
	percent_right_col = 100 - (percent_left_col+percent_middle_col);
	percent_top_row = 12;
	percent_bottom_row = 100 - percent_top_row;
	
	width_left_col = percent_left_col*0.01*width;
	width_middle_col = percent_middle_col*0.01*width;
	width_right_col = percent_right_col*0.01*width;
	height_top_row = percent_top_row*0.01*height;
	height_bottom_row = percent_bottom_row*0.01*height;

	chartWidth = width_right_col+width_middle_col - chartMargin.left - chartMargin.right;
	chartHeight = height_bottom_row - chartMargin.top - chartMargin.bottom;


	// slider parameters
	sliderWidth = 50;
	sliderHeight = chartHeight;
	sliderMin = yMin;
	sliderMax = yMax;
	sliderstart = startTime;
	sliderOffsetLeft = chartWidth + chartMargin.left + 10;
	sliderOffsetTop = chartMargin.top;
	handleWidth = 20;
}


function resize() {
	// console.log('resize');

	calcDimensions();

	scoreboard
		.attr("transform", "translate(" + (-width_middle_col+chartMargin.left+chartWidth/2-sbwidth/2 ) + "," + (sbtopmargin) + ")");


	chartsvg.attr("width", frameWidth)
	.attr("height", frameHeight);

	section_gameinfo.attr("transform", "translate(" + (margin.left) + "," + (margin.top) + ")");
	rect_gameinfo.attr("width", width_left_col+width_middle_col)
		.attr("height", height_top_row);

	section_display.attr("transform", "translate(" + (margin.left+width_left_col+width_middle_col) + "," + (margin.top) + ")");
	rect_display.attr("width", width_right_col)
		.attr("height", height_top_row);

	section_players.attr("transform", "translate(" + (margin.left) + "," + (margin.top+height_top_row) + ")");
	rect_players.attr("width", width_left_col)
		.attr("height", height_bottom_row);

	section_graph.attr("transform", "translate(" + (margin.left+width_left_col) + "," + (margin.top+height_top_row) + ")");
	rect_graph.attr("width", width_middle_col+width_right_col)
		.attr("height", height_bottom_row);

	// resize chart
	xScale
		.domain([-numGoals, numGoals])
		.range([0, chartWidth]);
	yScale
		.domain([yMin, yMax])
		.range([0, chartHeight]);
	
	xAxis.call(getAxisX.tickValues(getGoalTicks(numGoals, chartWidth)));
	yAxis.call(getAxisY);

	xGrid.call(getGridX.tickSize(-chartHeight, 0, 0).tickValues(getGoalTicks(numGoals)))
	yGrid.call(getGridY.tickSize(-chartWidth, 0, 0) )


	centerline.attr("d", valueline([{ "time": 0, "scoreDiff": 0 }, { "time": yMax, "scoreDiff": 0 }]));
	halftimeLine.attr("d", valueline([{ "time": yMax / 2, "scoreDiff": -numGoals }, { "time": yMax / 2, "scoreDiff": numGoals }]));

	sliderBox.attr("transform", "translate(" + sliderOffsetLeft + "," + sliderOffsetTop + ")");

	populateData(gamedata, startTime);

}



// =========== polyfill ============

if (!Array.prototype.findIndex) {
	Object.defineProperty(Array.prototype, 'findIndex', {
		value: function(predicate) {
			'use strict';
			if (this == null) {
				throw new TypeError('Array.prototype.findIndex called on null or undefined');
			}
			if (typeof predicate !== 'function') {
				throw new TypeError('predicate must be a function');
			}
			var list = Object(this);
			var length = list.length >>> 0;
			var thisArg = arguments[1];
			var value;

			for (var i = 0; i < length; i++) {
				value = list[i];
				if (predicate.call(thisArg, value, i, list)) {
					return i;
				}
			}
			return -1;
		},
		enumerable: false,
		configurable: false,
		writable: false
	});
}

// Add a getElementsByClassName function if the browser doesn't have one
// Limitation: only works with one class name
// Copyright: Eike Send http://eike.se/nd
// License: MIT License
// copied from https://gist.github.com/eikes/2299607

if (!document.getElementsByClassName) {
	document.getElementsByClassName = function(search) {
		var d = document, elements, pattern, i, results = [];
		if (d.querySelectorAll) { // IE8
			return d.querySelectorAll("." + search);
		}
		if (d.evaluate) { // IE6, IE7
			pattern = ".//*[contains(concat(' ', @class, ' '), ' " + search + " ')]";
			elements = d.evaluate(pattern, d, null, 0, null);
			while ((i = elements.iterateNext())) {
				results.push(i);
			}
		} else {
			elements = d.getElementsByTagName("*");
			pattern = new RegExp("(^|\\s)" + search + "(\\s|$)");
			for (i = 0; i < elements.length; i++) {
				if ( pattern.test(elements[i].className) ) {
					results.push(elements[i]);
				}
			}
		}
		return results;
	}
}



if (SVGElement.prototype.getElementsByClassName === undefined) {
	SVGElement.prototype.getElementsByClassName = function(className) {
		return this.querySelectorAll('.' + className);
	};
}

// ----------- END polyfill --------------------

var ballIconPath = "m 13.115534,9.9881529 1.57002,1.3488911 M 2.884465,6.924534 1.3144455,5.5756426 m 6.6710961,1.5572762 0,2.6626503 M 11.092465,14.955035 9.3489573,13.72121 10.408369,11.342436 m 2.707966,-1.33957 -2.717871,1.335272 L 7.9999995,9.7885418 5.6015341,11.338138 2.8836636,10.002866 c -0.031955,-1.0322124 -0.077459,-2.0608889 0,-3.0930452 L 5.6015341,5.5745493 7.9999995,7.1241448 10.398464,5.5745493 13.116335,6.9098208 c 0.0671,1.0315972 0.03074,2.0613406 0,3.0930452 z M 2.8844639,9.9881529 1.3144444,11.337044 M 4.9075332,14.955035 6.6510408,13.72121 5.591629,11.342436 m 1.0523066,2.341088 c 0.9040461,-0.0071 1.80808,-0.0032 2.7121269,0 m 3.7594715,-6.75899 1.57002,-1.3488914 M 11.092465,1.9576518 9.3489573,3.1914774 10.408369,5.570251 M 4.9075332,1.9576518 6.6510408,3.1914774 5.591629,5.570251 M 6.6439356,3.2291631 c 0.9040472,0.00616 1.808079,0.0028 2.7121269,0";

var suspensionIconPath = "m 1.5479708,2.0469633 c -0.0055,-0.3481 0.09473,-0.99431 0.757778,-1.08197996 0.663049,-0.0877 0.984979,0.41393996 1.023309,0.83681996 0.145432,1.6045 0.340924,3.35834 0.521594,5.3018 0.197975,-1.82049 0.271689,-3.59258 0.565139,-5.3573 0.05933,-0.35675 0.344945,-0.79349996 0.86522,-0.76732996 0.422562,0.0213 0.853325,0.47233996 0.837769,0.90341996 -0.07695,2.1324 -0.23198,4.58337 -0.267896,5.89491 0.02371,-0.7087 0.700297,-1.20414 1.250499,-1.08488 0.843158,0.18277 0.654307,1.0968 0.537517,1.70741 0.253651,-0.38414 0.722906,-0.78948 1.382941,-0.45856 0.660035,0.33092 0.416408,1.87304 0.427984,2.2358497 0.04906,1.53732 0.02933,0.97655 -0.152256,2.65827 -0.112823,1.04489 -0.764038,2.08284 -0.941425,3.09682 l -6.026729,0.02413 c -0.167857,6.7e-4 -1.239828,-2.40645 -1.75789398,-3.57345 -0.389664,-0.87776 1.23538198,-3.8101997 1.23538198,-3.8101997 0,0 -0.221374,-4.16231 -0.258932,-6.52573 z";


// ========= helpers =============

function formatTime(d) {
	var min = ~~(d / 60);
	var sec = ('00' + ~~(d % 60)).substr(-2);
	return min + ":" + sec;
}

function getGoalTicks( numGoals = 10 , width = 0) {
	var goalTicks = [];
	var j = 0;
	var d = Math.ceil(2*numGoals / (width/20));
	// console.log(d);
	for (var i = -numGoals; i <= numGoals; i++) {
		// console.log(width/numGoals);
		if (i%d == 0 | width == 0) {
			goalTicks[j++] = i;
		}
	}
	//console.log(goalTicks);
	return goalTicks;
}
function getTimeTicks() {
	var timeTicks = new Array();
	for (i = 0; i < 13; i++) {
		timeTicks[i] = i * 300;
	}
	return timeTicks;
}


function filterData(data, endTime) {
	// console.log(data)
	//TODO use .filter() instead
	var endTime = (typeof endTime !== 'undefined') ? endTime : yMax;

	return data.filter(function(d) {
		var curr = d.time;
		return (d.scoreChange) && curr <= endTime;
	})
}


function getMaxGoalDiff(gamedata) {
	if (gamedata !== null) {
			var maxDiff = 0;
		for (var i = gamedata.length - 1; i >= 0; i--) {
			//console.log(Math.abs(gamedata[i].scoreDiff));
			if (Math.abs(gamedata[i].scoreDiff) > maxDiff) {
				maxDiff = Math.abs(gamedata[i].scoreDiff);
			}
		}
		return maxDiff;
	}
	return 0;
}

function getPlayerAlias(name) {
	// console.log(name);
	if (name !== null) {
		// escape regex characters
		var alias = name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
		alias = alias.trim();
		// ÄäÖöÜüß
		alias = alias.replace(/Ä/g, 'Ae');
		alias = alias.replace(/ä/g, 'ae');
		alias = alias.replace(/Ö/g, 'Oe');
		alias = alias.replace(/ö/g, 'oe');
		alias = alias.replace(/Ü/g, 'Ue');
		alias = alias.replace(/ü/g, 'ue');
		alias = alias.replace(/ß/g, 'ss');
		
		alias = alias.replace(/ /g, '-');

		alias = alias.toLowerCase();
		// console.log(name, alias);
		return alias;
	}
	return '';
}

function getTeamIndicator(d) {
	if (d > 0) {
		return "home"
	}
	if (d < 0) {
		return "away"
	}
	if (d == 0) {
		return "neutral"
	}
}

function getActionText(item) {
	//format text for action
	var text = item.text;
	if (item.name !== null) {
		text = item.name;
	}
	if (item.category === 'timeout') {
		text = 'Auszeit';
	}
	return text;
}

function getInfoText(item) {
	//format text for action
	var text = '';
	if (item.scoreChange === 1) {
		text = "Tor zum  " + item.scoreHome + ":" + item.scoreAway;
		if (item.seven) {
			text = text + " (7m)";
		}
	}
	if (item.category === 'yellow') {
		text = "Gelbe Karte";
	}
	if (item.category === 'red') {
		text = "Rote Karte";
	}
	if (item.category === 'suspension') {
		text = "2-min-Strafe";
	}
	if (item.text.indexOf('Spiel') === 0) {
		text = '';
	}
	if (item.category === 'timeout') {
		text = item.text.replace('Auszeit ', '');
	}
	return text;
}

function findLatestAction(data, endTime) {
	var recent = 0;
	data.forEach(function(d, i) {
		if (d.time > endTime) {
			return recent;
		}
		recent = i;
	});
	return recent; //TODO fix race error, return in callback
}

function addPlayersPortraits(playersdata, picarray) {
	// console.log(picarray);
	playersdata.forEach(function (element, i) {
		// console.log(element);
		var picIndex;
		if (default_players_sprite) {
			picIndex = 0;
		} else {
			picIndex = picarray.findIndex(function(d) {
				return d === element.alias;
			});
			// console.log(picIndex);
			if (picIndex == '-1') {
				picIndex = picarray.findIndex(function(d) {
					return d === 'dummy';
				});
			}
		}
		playersdata[i].picIndex = picIndex;
	}); 
	// console.log(playersdata);
	return playersdata;
}

function getPlayersPicWidthFactor() {
	if (default_players_sprite) return 1;
	return picarray.length;
}


function getIconX(player, category) {
	var x = -1;
	var delta = 15;
	var cats = ['yellow', 'suspension1', 'suspension2', 'suspension3', 'red'];
	for (var i = 0; i <= category; i++) {
		if (player[cats[i]]) {
			x++;
		}
	}
	return x * delta;
}



function addPlayerProfiles (playerdata) {

	var playerprofiles = playerProfileBox.selectAll(".test").data(playerdata)
		.enter().append("g")
		.attr("id", function(d) {
			return "profile-" + d.alias;
		})
		.attr("class", "pic")
		.attr("transform", function(d, i) {
			return "translate(" + 0 + "," + (i * (2.3 * picRadius)) + ")";
		})
		.on("mouseover", function(d) {
			// console.log(d.alias); 
			// TODO why no bechthold
			showScore(d.alias);
		})
		.on("mouseout", function(d) { hideScore(); });


	playerprofiles.append("defs")
		.append("pattern")
		.attr("id", function(d, i) {
			return "profilepic-" + i;
		})
		.attr("height", 1)
		.attr("width", 1)
		.attr("x", "0")
		.attr("y", "0").append("image")
		.attr("x", function(d, i) {
			return -d.picIndex * (picRadius * 2);
		})
		.attr("y", 0)
		.attr("width", picRadius * 2 * picWidthFactor)
		.attr("height", picRadius * 2)
		.attr("xlink:href", players_sprite);

	playerprofiles.append("circle")
		.attr("r", picRadius)
		.attr("cx", picRadius + 40)
		.attr("cy", picRadius)
		.attr("fill", function(d, i) {
			return "url(#profilepic-" + i + ")";
		});

	playerprofiles.append("text")
		.attr("class", "action number bg")
		.attr("x", 50)
		.attr("y", 40)
		.text(function(d) {
			return d.number;
		});
	playerprofiles.append("text")
		.attr("class", "action number fg")
		.attr("x", 52)
		.attr("y", 42)
		.text(function(d) {
			return d.number;
		});

	playerprofiles.append("text")
		.attr("class", "profile name")
		.attr("x", (picRadius * 2.5 + 40))
		.attr("y", 12)
		.text(function(d) {
			return d.name;
		});

	var goalicons = playerprofiles.append("g")
		.attr("class", "icons")
		.attr("transform", function(d) {
			var x = 0;
			x = (picRadius * 2.5 + 45);

			return "translate(" + x + "," + 20 + ")";
		})
		.style("opacity", 0.8);


	var playericons = playerprofiles.append("g")
		.attr("class", "icons")
		.attr("transform", function(d) {
			var x = 0;
			// console.log('#profile-'+d.alias);

			var bbox = document.getElementById('profile-' + d.alias)
				.getElementsByClassName('name')[0]
				.getBBox();
			// console.log(bbox.width);

			x = picRadius * 2.5 + 40 + bbox.width + 10;
			var y = -2;

			return "translate(" + x + "," + y + ")";
		})
		.style("opacity", 0.8);



	playericons
		.filter(function(d) {
			// console.log(d.yellow, Boolean(d.yellow));
			return d.yellow;
		})
		.append('rect')
		.attr("class", "card yellowcard")
		.attr("width", 10)
		.attr("height", 15)
		.attr("x", function(d) {
			return getIconX(d, 0);
		})
		.attr("y", 0.5)
		.attr("rx", 2)
		.attr("ry", 2);

	playericons
		.filter(function(d) {
			// console.log(d.red);
			return d.red;
		})
		.append('rect')
		.attr("class", "card redcard")
		.attr("width", 10)
		.attr("height", 15)
		.attr("x", function(d) {
			return getIconX(d, 4);
		})
		.attr("y", 0.5)
		.attr("rx", 2)
		.attr("ry", 2);


	var ball = goalicons
		.filter(function(d) {
			// console.log(d);
			return d.goals;
		}).append("g")
		.attr("class", "ball")
		.attr("transform", "translate(" + 0 + "," + 0 + "), scale(0.7)");


	ball
		.each(function(d) {
			var g = d3.select(this);
			// console.log(d3.select("#profile-luis-herre").data());
			// console.log(g.data()[0].goals);
			var goals = g.data()[0].goals;
			for (var i = 0; i < goals; i++) {
				var innerG = g.append("g")
					.attr("class", "goal-" + i)
					.attr("transform", "translate( " + 18 * i + "," + 0 + " )");

				innerG.append("circle")
					.attr("class", "goal-" + i)
					.attr("cx", 8)
					.attr("cy", 8.45)
					.attr("r", 7.5);


				innerG.append("path")
					.attr("d", ballIconPath);

			}
		});


	playericons
		.filter(function(d) {
			// console.log(d);
			return d.suspension1;
		})
		.append('path')
		.attr("class", "suspension bench1")
		.attr("transform", function(d) {
			return "translate(" + getIconX(d, 1) + "," + 0 + ")";
		})
		.attr("d", suspensionIconPath);

	playericons
		.filter(function(d) {
			// console.log(d);
			return d.suspension2;
		})
		.append('path')
		.attr("class", "suspension bench2")
		.attr("transform", function(d) {
			return "translate(" + getIconX(d, 2) + "," + 0 + ")";
		})
		.attr("d", suspensionIconPath);

	playericons
		.filter(function(d) {
			// console.log(d);
			return d.suspension3;
		})
		.append('path')
		.attr("class", "suspension bench3")
		.attr("transform", function(d) {
			return "translate(" + getIconX(d, 3) + "," + 0 + ")";
		})
		.attr("d", suspensionIconPath);
}
// ----------- END helpers --------------------



// ============= main =================

// console.log("Game Graph");

// create an svg container
var chartsvg = d3.select("#gamegraphframe")
	.append("svg:svg")
	.attr("id", "gamegraph")
	.attr("class", "hidden"); // only make visible if data loaded

// Append 'g' for the different sections
var section_gameinfo = chartsvg.append("g");
var rect_gameinfo = section_gameinfo.append('rect')
	// .attr("fill", "red")
	.attr("class", "section gameinfo");
	
var section_display = chartsvg.append("g");
var rect_display = section_display.append('rect')
	// .attr("fill", "green")
	.attr("class", "section display");
	
var section_players = chartsvg.append("g");
var rect_players = section_players.append('rect')
	// .attr("fill", "blue")
	.attr("class", "section players");
	
var section_graph = chartsvg.append("g");
var rect_graph = section_graph.append('rect')
	// .attr("fill", "orange")
	.attr("class", "section graph");


var playerProfileBox = section_players.append("g")
	.attr("id", "playerprofiles")
	.attr("transform", "translate(" + 0 + "," + chartMargin.top + ")");

// Graph setup

// define the x scale  (vertical)
var xScale = d3.scale.linear()
var yScale = d3.scale.linear()


// define the x axis
var getAxisX = d3.svg.axis()
	.orient("top")
	.tickValues(getGoalTicks())
	.tickFormat(function(d) {
		return Math.abs(d);
	})
	.tickSize(5)
	.scale(xScale);
var getGridX = d3.svg.axis()
	.orient("top")
	.tickValues((getGoalTicks()))
	.tickFormat("")
	.scale(xScale);

// define the y axis
var getAxisY = d3.svg.axis()
	.orient("left")
	.tickValues(getTimeTicks())
	.tickFormat(function(d) {
		return formatTime(d);
	})
	.tickSize(5)
	.scale(yScale);
var getGridY = d3.svg.axis()
	.orient("left")
	.tickValues(getTimeTicks())
	.tickFormat("")
	.scale(yScale);



// Append 'g' in a place that is the actual area for the graph
var chart = section_graph.append("g")
	.attr("transform", "translate(" + chartMargin.left + "," + chartMargin.top + ")");

// draw axis 
var xAxis = chart.append("g")
	.attr("class", "axis xaxis");
var yAxis = chart.append("g")
	.attr("class", "axis yaxis");
// draw grid
var xGrid = chart.append("g")
	.attr("class", "grid");
var yGrid = chart.append("g")
	.attr("class", "grid");


var scoreline = chart.append("path")
	.attr("id", "scoreline")
	.attr("class", "line");

var valueline = d3.svg.line()
	.x(function(d) {
		return xScale(parseInt(d.scoreDiff));
	})
	.y(function(d) {
		return yScale(d.time);
	});

// Add centerline
var centerline = chart.append("path")
	.attr("class", "center-line");
// Add halftime line
var halftimeLine = chart.append("path")
	.attr("class", "halftime-line");
	

var actionTagBox = section_graph.append("g")
	.attr("id", "allactiontags");


var scoreboard = section_display.append("g")
	.attr("id", "scoreboard")
	.attr("class", "score");

// =========== slider ============

// scale function for slider
var timeScale = d3.scale.linear()
	// .domain([yMin, yMax]) //TODO
 // .range([0, 111  ])
	.clamp(true);

var brush = d3.svg.brush();

var sliderBox = section_graph.append("g")
	.attr("class", "sliderbox");

var sliderAxis = sliderBox.append("g")
		.attr("class", "y axis")

var slider = sliderBox.append("g")
	.attr("class", "slider");

var handle = slider.append("g")
	.attr("class", "handle");

var brushed;


function initializeGraph() {
	// console.log('initializeGraph');

	initializeScoreboard();

	timeScale
		.domain([sliderMin, sliderMax])
		// .domain([0, 100])
		.range([0, sliderHeight]);

	brush
		.y(timeScale)
		.extent([sliderstart, sliderstart]);

	// defines brush
	brush.on("brush", brushed);

	sliderAxis
		// put in middle of screen
		.attr("transform", "translate(" + sliderWidth / 2 + "," + 0 + ")")
		// inroduce axis
		.call(d3.svg.axis()
				.scale(timeScale)
				.orient("left")
				.tickSize(0)
				.tickPadding(12)
				.tickValues([timeScale.domain()[0], timeScale.domain()[1]])
				.tickFormat(function(d) {
					return formatTime(d);
				})
			)
		.select(".domain")
		.select(function() {
			// console.log(this);
			return this.parentNode.appendChild(this.cloneNode(true));
		})
		.attr("class", "halo");


	slider.selectAll(".extent,.resize")
		.remove();

	slider.select(".background")
		.attr("transform", "translate(" + 0 + "," + 0 + ")")
		.style("cursor", "pointer")
		.attr("width", sliderWidth);

	handle.append("path")
		.attr("transform", "translate(" + sliderWidth / 2 + "," + 0 + ")")
		.attr("d", "M " + (-handleWidth) + " 0 H " + handleWidth)

	handle.append('text')
		.text(sliderstart)
		.attr("transform", "translate(" + sliderWidth + "," + 6 + ")");


	sliderBox.attr("class", "sliderbox");

	slider.call(brush);

	// slider
	// 	.call(brush.event);
}



function updateData(selectedGameId, callback) {

	// Game report
	var rows = Array.from(document.getElementsByClassName("gameInfo"));
	// console.log(rows);
	rows.forEach(function(element) {
			// console.log(element);
			element.classList.add("hidden");
		})

	document.getElementById('gameReport_'+selectedGameId).classList.remove("hidden");

	// Game Graph

	data = gamesData[selectedGameId];
	// console.log(data);
	// console.log(JSON.stringify(data,null,4));

	gameinfo = data.gameinfo;
	gamedata = data.gamedata;
	playerdata = data.playerdata;
	picarray = data.picarray;
	
	playerdata = addPlayersPortraits(playerdata, picarray);
	picWidthFactor = getPlayersPicWidthFactor();

	/* clean up previous data */
	
	document.getElementById('gamegraph').classList.add("hidden");
	
	// remove previous player profiles
	while (playerProfileBox[0][0].firstChild) {
		// console.log(playerProfileBox[0][0].firstChild);
		playerProfileBox[0][0].removeChild(playerProfileBox[0][0].firstChild);
	}	
	// remove previous action tags
	while (actionTagBox[0][0].firstChild) {
		// console.log(playerProfileBox[0][0].firstChild);
		actionTagBox[0][0].removeChild(actionTagBox[0][0].firstChild);
	}
	
	// console.log(gamedata);
	if (gamedata !== undefined && gamedata.length > 0) {
		// console.log(gamedata);

		document.getElementById('gamegraph-box').classList.remove("hidden");
		
		numGoals = getMaxGoalDiff(gamedata)+1;

		resize();

		document.getElementById('gamegraph').classList.remove("hidden");
		// console.log(chartsvg);
		// scoreboard.classList.remove("hidden");
		gamegraph.classList.remove("hidden");
	
		callback(selectedGameId);
	} else {
		document.getElementById('gamegraph-box').classList.add("hidden");
	}
}

function loadData(selectedGameId) {
	
	// console.log(selectedGameId);

	updateGraph(gamedata, 3600);
	if(playerdata !== null) addPlayerProfiles(playerdata);
	
	// // =========== slider ============

	brushed = function() {
		var value = brush.extent()[0];
		// console.log(value);
		if (d3.event.sourceEvent) {
			value = timeScale.invert(d3.mouse(this)[1]);
			brush.extent([value, value]);
		}
		// only full integer: ~~()
		updateEndTime(gamedata, ~~(value));
		var currIndex = findLatestAction(gamedata, ~~(value));
		updateScoreBoard(gamedata[currIndex]);
		handle.attr("transform", "translate(0," + timeScale(value) + ")");
		handle.select('text').text(~~(value));
	}

	// // defines brush
	brush.on("brush", brushed);

	slider.call(brush);

	slider.selectAll(".extent,.resize")
		.remove();

	slider.select(".background")
		.attr("transform", "translate(" + 0 + "," + 0 + ")")
		.style("cursor", "pointer")
		.attr("width", sliderWidth);

	// handle = slider.append("g")
	// 	.attr("class", "handle");

	// handle.append("path")
	// 	.attr("transform", "translate(" + sliderWidth / 2 + "," + 0 + ")")
	// 	.attr("d", "M " + (-handleWidth) + " 0 H " + handleWidth)

	// handle.append('text')
	// 	.text(sliderstart)
	// 	.attr("transform", "translate(" + sliderWidth + "," + 6 + ")");

	slider
		.call(brush.event);

}



function updateGraph(data, endTime) {
	// console.log(endTime);
	populateData(data, endTime);
	addActionTags(data);

}

function updateEndTime(data, endTime) {
	// console.log(endTime);

	scoreline
		.attr("d", valueline(filterData(data, endTime)));

	chart.selectAll(".goaldot")
		.classed("showGoal", function(d) {
			return (d.time > endTime);
		})

	if (endTime < 3600) {
		var curr = findLatestAction(data, endTime);
		showScore(data[curr].actionIndex);
	}
}


function populateData(data, endTime) {
	// console.log(endTime);

	scoreline
		.attr("d", valueline(filterData(data, endTime)));


	// TODO is selectAll(".point") necessary
	chart.selectAll("#goaldots").remove();

	chart.append("g").attr("id", "goaldots").selectAll(".point").data(filterData(data, endTime))
		.enter().append("svg:circle")
		.attr("class", function(d) {
			return d.alias;
		})
		.classed("goaldot", true)
		.attr("cx", function(d) {
			return xScale(d.scoreDiff)
		})
		.attr("cy", function(d) {
			return yScale(d.time)
		})
		.attr("r", 3)
		.on("mouseover", function(d) { showScore(d.actionIndex); })
		.on("mouseout", function(d) { hideScore(); });
}

function showScore(item) {
	hideScore();
	var className;
	// console.log(item);
	if (typeof item == 'string') {
		// name
		className = item;
	} else {
		className = "event-" + item;
	}
	// console.log(className);
	section_graph.selectAll("." + className)
		.classed("hideactiontag", false);

}


function hideScore() {
	// console.log('hide');
	section_graph.selectAll(".actionTagItem")
		.classed("hideactiontag", true);
}

var addActionTags = function(data) {

	var widthAnnotations = 200;
	var widthIcon = 8;
	var widthTime = 57;
	var annotationGap = 8;

	//TODO is selectAll necessary?
	actionTagGroups = actionTagBox.selectAll(".actiontagtest").data(data)
		.enter().append("g")
		.attr("transform", function(d) {
			return "translate(" + (xScale(-numGoals)-widthAnnotations+chartMargin.left-annotationGap) + "," + (chartMargin.top+yScale(d.time)) + ")";
		})
		.attr("class", function(d) {
			return "actionTagItem icons " + getPlayerAlias(d.name) + " event-" + d.actionIndex + " " + getTeamIndicator(d.team);
		})
		.classed("hideactiontag", true);

	actionTagGroups.append("rect")
		.attr("class", "bg")
		.attr("x", 0)
		.attr("y", -8)
		.attr("width", widthAnnotations)
		.attr("height", 16)
		.attr("fill", "white");

	actionTagGroups.append("text")
		.attr("class", "time")
		.attr("dx", widthAnnotations)
		.attr("dy", 5)
		.text(function(d) {
			return d.timeString;
		});

	actionTagGroups.append("text")
		.attr("class", "name")
		.attr("dx", widthAnnotations-widthIcon-widthTime)
		.attr("dy", 5)
		.text(function(d) {
			return getActionText(d);
		});

	actionTagGroups.append("text")
		.attr("class", "info")
		.attr("dx", widthAnnotations-widthIcon-widthTime)
		.attr("dy", 20)
		.text(function(d) {
			return getInfoText(d);
		});

	actionTagGroups
		.filter(function(d) {
			// console.log(d.category);
			return d.category === 'yellow';
		})
		.append('rect')
		.attr("class", "card yellowcard")
		.attr("width", 10)
		.attr("height", 15)
		.attr("x", widthAnnotations-widthTime+2)
		.attr("y", -8)
		.attr("rx", 2)
		.attr("ry", 2);

	actionTagGroups
		.filter(function(d) {
			// console.log(d.category);
			return d.category === 'red';
		})
		.append('rect')
		.attr("class", "card redcard")
		.attr("width", 10)
		.attr("height", 15)
		.attr("x", widthAnnotations-widthTime+2)
		.attr("y", -8)
		.attr("rx", 2)
		.attr("ry", 2);

	var actionballs = actionTagGroups
		.filter(function(d) {
			// console.log(d.category);
			return (d.category === 'goal') || (d.category === 'penalty' && d.scoreChange);
		}).append("g")
		.attr("class", "ball")
		.attr("transform", "translate(" + (widthAnnotations-widthTime) + "," + (-7) + "), scale(0.9)");

	actionballs
		.append("circle")
		.attr("class", "goal")
		.attr("cx", 8)
		.attr("cy", 8.5)
		.attr("r", 7.5);

	actionballs
		.append("path")
		.attr("d", ballIconPath);

	actionballs
		.filter(function(d) {
			return (d.category === 'penalty' && d.scoreChange);
		}).append("text")
		.attr("class", "sevenicon")
		.attr("dx", 13)
		.attr("dy", 14)
		.text("7");

	actionTagGroups
		.filter(function(d) {
			// console.log(d.category);
			return d.category === 'suspension';
		})
		.append('path')
		.attr("class", "suspension")
		.attr("transform", function(d) {
			return "translate(" + (widthAnnotations-widthTime+2) + "," + -8 + ")";
		})
		.attr("d", suspensionIconPath);



}




function initializeScoreboard() {
	scoreboard.append("rect")
		.attr("class", "background")
		.attr("width", sbwidth + "px")
		.attr("height", sbheight + "px")
		.attr("rx", sbradius)
		.attr("ry", sbradius)
		.text("0");
	scoreboard.append("rect")
		.attr("class", "frame")
		.attr("x", sboffset + "px")
		.attr("y", sboffset + "px")
		.attr("width", (sbwidth - 2 * sboffset) + "px")
		.attr("height", (sbheight - 2 * sboffset) + "px")
		.attr("rx", sbradius)
		.attr("ry", sbradius)
		.text("0");
	scoreboard.append("rect")
		.attr("class", "background")
		.attr("x", (2 * sboffset) + "px")
		.attr("y", (2 * sboffset) + "px")
		.attr("width", (sbwidth - 4 * sboffset) + "px")
		.attr("height", (sbheight - 4 * sboffset) + "px")
		.attr("rx", sbradius)
		.attr("ry", sbradius)
		.text("0");

	scoreboard.append("text")
		.attr("class", "team")
		.attr("x", sbHome + "px")
		.attr("y", (sbscoreline - 32) + "px")
		.text("HEIM");

	scoreboard.append("text")
		.attr("class", "team")
		.attr("x", sbAway + "px")
		.attr("y", (sbscoreline - 32) + "px")
		.text("GAST");

	scoreboard.append("text")
		.attr("class", "diget time bg")
		.attr("x", (sbwidth / 2) + "px")
		.attr("y", sbTime + "px")
		.text("88 88");

	scoreboard.append("text")
		.attr("class", "diget time bg")
		.attr("x", (sbwidth / 2) + "px")
		.attr("y", sbTime + "px")
		.text(":");

	scoreboard.append("text")
		.attr("id", "scoreTime")
		.attr("class", "diget time")
		.attr("x", (sbwidth / 2) + "px")
		.attr("y", sbTime + "px")
		.text("0");

	scoreboard.append("text")
		.attr("class", "diget score bg")
		.attr("x", sbHome + "px")
		.attr("y", sbscoreline + "px")
		.text("88");

	scoreboard.append("text")
		.attr("id", "scoreHome")
		.attr("class", "diget score")
		.attr("x", sbHome + "px")
		.attr("y", sbscoreline + "px")
		.text("0");

	scoreboard.append("text")
		.attr("class", "diget score bg")
		.attr("x", sbAway + "px")
		.attr("y", sbscoreline + "px")
		.text("88");

	scoreboard.append("text")
		.attr("id", "scoreAway")
		.attr("class", "diget score")
		.attr("x", sbAway + "px")
		.attr("y", sbscoreline + "px")
		.text("0");
}


function updateScoreBoard(score) {
	scoreboard.select("#scoreHome")
		.text(score.scoreHome);
	scoreboard.select("#scoreAway")
		.text(score.scoreAway);
	scoreboard.select("#scoreTime")
		.text(score.timeString.replace(':', ' '));
}



document.getElementById('gameSelect').onchange = function(){
	var selectedGameId = this.value;
	updateData(selectedGameId, loadData);
};


d3.select(window).on('resize', resize); 


});

