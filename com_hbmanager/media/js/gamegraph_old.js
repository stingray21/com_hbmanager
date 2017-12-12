jQuery(document).ready(function($) {


// ============= definitions =================

// dimensions
var margin = { top: 170, right: 100, bottom: 50, left: 500 };
var width;
var divWidth = 800;

var yMin = 0;
var yMax = 3600;

// var divWidth = parseInt(d3.select('#gamegraph').style('width'), 10);
//console.log(divWidth);
width = divWidth - margin.left - margin.right;
var height;
var divHeight = 900;
height = divHeight - margin.top - margin.bottom;

var startTime = 3600;
var nrGoals = 10;

// slider parameters
var sliderWidth = 50;
var sliderHeight = height;
var sliderMin = yMin;
var sliderMax = yMax;
var sliderstart = startTime;
var sliderOffsetLeft = width + margin.left + 10;
var sliderOffsetTop = margin.top;
var handleWidth = 20;


var ballIconPath = "m 13.115534,9.9881529 1.57002,1.3488911 M 2.884465,6.924534 1.3144455,5.5756426 m 6.6710961,1.5572762 0,2.6626503 M 11.092465,14.955035 9.3489573,13.72121 10.408369,11.342436 m 2.707966,-1.33957 -2.717871,1.335272 L 7.9999995,9.7885418 5.6015341,11.338138 2.8836636,10.002866 c -0.031955,-1.0322124 -0.077459,-2.0608889 0,-3.0930452 L 5.6015341,5.5745493 7.9999995,7.1241448 10.398464,5.5745493 13.116335,6.9098208 c 0.0671,1.0315972 0.03074,2.0613406 0,3.0930452 z M 2.8844639,9.9881529 1.3144444,11.337044 M 4.9075332,14.955035 6.6510408,13.72121 5.591629,11.342436 m 1.0523066,2.341088 c 0.9040461,-0.0071 1.80808,-0.0032 2.7121269,0 m 3.7594715,-6.75899 1.57002,-1.3488914 M 11.092465,1.9576518 9.3489573,3.1914774 10.408369,5.570251 M 4.9075332,1.9576518 6.6510408,3.1914774 5.591629,5.570251 M 6.6439356,3.2291631 c 0.9040472,0.00616 1.808079,0.0028 2.7121269,0";

var suspensionIconPath = "m 1.5479708,2.0469633 c -0.0055,-0.3481 0.09473,-0.99431 0.757778,-1.08197996 0.663049,-0.0877 0.984979,0.41393996 1.023309,0.83681996 0.145432,1.6045 0.340924,3.35834 0.521594,5.3018 0.197975,-1.82049 0.271689,-3.59258 0.565139,-5.3573 0.05933,-0.35675 0.344945,-0.79349996 0.86522,-0.76732996 0.422562,0.0213 0.853325,0.47233996 0.837769,0.90341996 -0.07695,2.1324 -0.23198,4.58337 -0.267896,5.89491 0.02371,-0.7087 0.700297,-1.20414 1.250499,-1.08488 0.843158,0.18277 0.654307,1.0968 0.537517,1.70741 0.253651,-0.38414 0.722906,-0.78948 1.382941,-0.45856 0.660035,0.33092 0.416408,1.87304 0.427984,2.2358497 0.04906,1.53732 0.02933,0.97655 -0.152256,2.65827 -0.112823,1.04489 -0.764038,2.08284 -0.941425,3.09682 l -6.026729,0.02413 c -0.167857,6.7e-4 -1.239828,-2.40645 -1.75789398,-3.57345 -0.389664,-0.87776 1.23538198,-3.8101997 1.23538198,-3.8101997 0,0 -0.221374,-4.16231 -0.258932,-6.52573 z";


// url to get the chart data
// var url = "./hbdata/hkog.json";
var url = "http://localhost/handball/hb_joomla3/hbdata/hkog.json";
// var url = "index.php?option=com_hbteam&task=getGameChartData&format=raw" + "&teamkey=" + teamkey + "&season=" + season + "&gameId=" + gameId;
//console.log(url);

// url to sprite sheet file
var players_sprite = 'http://localhost/handball/hb_joomla3/hbdata/playersprites.png';
// var players_sprite = '../../../images/handball/'+teamkey+'_playersprites.png';
// console.log(players_sprite);


// scoreboard
var sbheight = 100;
var sbwidth = 150;
var sbtopmargin = 20;
var sbscoreline = 88;
var sbTime = 37;
var sbHome = 53;
var sbAway = 135;
var sbradius = 8;
var sboffset = 2;

// player profiles

var picRadius = 22;
var profilesOffset = { "left": 30, "top": margin.top };
// ========= helpers =============

function formatTime(d) {
    var min = ~~(d / 60);
    var sec = ('00' + ~~(d % 60)).substr(-2);
    return min + ":" + sec;
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

function filterData(data, endTime) {
    // console.log(data)
    //TODO use .filter() instead
    var endTime = (typeof endTime !== 'undefined') ? endTime : yMax;

    return data.filter(function(d) {
        var curr = d.time;
        return (d.scoreChange) && curr <= endTime;
    })
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

function getPlayerAlias(name) {
    //console.log(name);
    if (name !== null) {
        var alias = name;
        alias = alias.trim();
        // ÄäÖöÜüß
        alias = alias.replace("Ä", "Ae");
        alias = alias.replace("ä", "ae");
        alias = alias.replace("Ö", "Oe");
        alias = alias.replace("ö", "oe");
        alias = alias.replace("Ü", "Ue");
        alias = alias.replace("ü", "ue");
        alias = alias.replace("ß", "ss");

        alias = alias.replace(" ", "-");
        alias = alias.toLowerCase();
        return alias;
    }
    return '';
}


function getIconX(player, category) {
    var x = -1;
    var delta = 15;
    var cats = ['gelb', 'zweiMin1', 'zweiMin2', 'zweiMin3', 'rot'];
    for (var i = 0; i <= category; i++) {
        // console.log(player[cats[i]]);
        if (player[cats[i]] != null) {
            x++;
        }
    }
    return x * delta;
}


function getPicIndex(name, total) {

    // console.log(name);


    var index;
    index = picarray.findIndex(function(d) {
        return d === name;
    });
     console.log(index);
    if (index == '-1') {
        index = picarray.findIndex(function(d) {
            return d === 'dummy';
        });
    }

    // console.log(index);
    return index;
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
// ============= main =================

// console.log("Game Graph");

//preloading images
var preloadedImage = new Image();
preloadedImage.src = players_sprite;


// scale function for slider
var timeScale = d3.scale.linear()
    .domain([sliderMin, sliderMax])
    .range([0, sliderHeight])
    .clamp(true);


var brush = d3.svg.brush()
    .y(timeScale)
    .extent([sliderstart, sliderstart]);


d3.json(url, function(error, data) {
    if (error != null) { console.log(error) };
    // console.log(data);
    if (typeof data != 'undefined' || data.playerdata !== null) {

        gameinfo = data.gameinfo;
        // console.log(gameinfos);

        writeGameInfo(gameinfo);
 
        gamedata = data.gamedata;
        // console.log(gamedata);
        playerdata = data.playerdata;
        picarray = data.picarray;

        // console.log(playerdata);
        nrGoals = getMaxGoalDiff(gamedata)+1;

        xScale
            .domain([-nrGoals, nrGoals])
            .range([0, width]);

        // console.log(gameinfo);
        chart.append("g")
            .attr("class", "teambanner")
            .attr("transform", "translate("+xScale(-nrGoals/2)+", "+yScale(yMax/2)+")")
            .append("text")
            .attr("transform", "rotate(-90)")
            .text(gameinfo.heim);
        
        xAxis.orient("top")
            .tickValues(function() {
                var goalTicks = [];
                var j = 0;
                for (var i = -nrGoals; i <= nrGoals; i++) {
                    if(nrGoals > 10 && i%2==0) {
                        goalTicks[j++] = i;
                    }
                }
                //console.log(goalTicks);
                return goalTicks;
            })
            .tickFormat(function(d) {
                return Math.abs(d);
            })
            .tickSize(5)
            .scale(xScale);
        
        // draw x axis 
        chart.select(".axis.xaxis")
            .call(xAxis);

        chart.select(".grid")
            .call(xAxis
                .tickSize(-height, 0, 0)
                .tickFormat("")
            )
 
        populateData(gamedata, startTime);

        // =========== slider ============

        var brushed = function() {
            var value = brush.extent()[0];

            if (d3.event.sourceEvent) {
                value = timeScale.invert(d3.mouse(this)[1]);
                brush.extent([value, value]);
            }
            // only full integer: ~~()
            updateData(gamedata, ~~(value));
            var currIndex = findLatestAction(gamedata, ~~(value));
            updateScoreBoard(gamedata[currIndex]);
            handle.attr("transform", "translate(0," + timeScale(value) + ")");
            handle.select('text').text(~~(value));
        }

        // defines brush
        brush.on("brush", brushed);


        var sliderBox = chartsvg.append("g")
            .attr("class", "sliderbox")
            .attr("transform", "translate(" + sliderOffsetLeft + "," + sliderOffsetTop + ")");

        sliderBox.append("g")
            .attr("class", "y axis")
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

        var slider = sliderBox.append("g")
            .attr("class", "slider")
            .call(brush);

        slider.selectAll(".extent,.resize")
            .remove();

        slider.select(".background")
            .attr("transform", "translate(" + 0 + "," + 0 + ")")
            .style("cursor", "pointer")
            .attr("width", sliderWidth);

        var handle = slider.append("g")
            .attr("class", "handle");

        handle.append("path")
            .attr("transform", "translate(" + sliderWidth / 2 + "," + 0 + ")")
            .attr("d", "M " + (-handleWidth) + " 0 H " + handleWidth)

        handle.append('text')
            .text(sliderstart)
            .attr("transform", "translate(" + sliderWidth + "," + 6 + ")");

        slider
            .call(brush.event)

        if(playerdata !== null) {
        var playerprofiles = playerprofilesbox.selectAll(".test").data(playerdata)
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
                return -getPicIndex(d.alias) * (picRadius * 2);
            })
            .attr("y", 0)
            .attr("width", picRadius * 2 * picarray.length)
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
                return d.trikotNr;
            });
        playerprofiles.append("text")
            .attr("class", "action number fg")
            .attr("x", 52)
            .attr("y", 42)
            .text(function(d) {
                return d.trikotNr;
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
                // console.log(d.gelb);
                return d.gelb != null;
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
                // console.log(d);
                return d.rot != null;
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
                return d.tore !== null;
            }).append("g")
            .attr("class", "ball")
            .attr("transform", "translate(" + 0 + "," + 0 + "), scale(0.7)");


        ball
            .each(function(d) {
                var g = d3.select(this);
                // console.log(d3.select("#profile-luis-herre").data());
                // console.log(g.data()[0].goals);
                var goals = g.data()[0].tore;
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
                return d.zweiMin1 != null;
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
                return d.zweiMin2 != null;
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
                return d.zweiMin3 != null;
            })
            .append('path')
            .attr("class", "suspension bench3")
            .attr("transform", function(d) {
                return "translate(" + getIconX(d, 3) + "," + 0 + ")";
            })
            .attr("d", suspensionIconPath);
    }


    } else {
        console.log('No data');
    }
});

// create an svg container
var chartsvg = d3.select("#gamegraph")
    .append("svg:svg")
    .attr("width", divWidth)
    .attr("height", divHeight);




// Append 'g' in a place that is the actual area for the graph
var chart = chartsvg.append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

// define the x scale  (vertical)
var xScale = d3.scale.linear()
    .domain([-nrGoals, nrGoals])
    .range([0, width]);

var yScale = d3.scale.linear()
    .domain([yMin, yMax])
    .range([0, height]);


// define the x axis
var xAxis = d3.svg.axis()
    .orient("top")
    .tickValues(function() {
        var goalTicks = [];
        var j = 0;
        for (var i = -nrGoals; i <= nrGoals; i++) {
            goalTicks[j++] = i;
        }
        //console.log(goalTicks);
        return goalTicks;
    })
    .tickFormat(function(d) {
        return Math.abs(d);
    })
    .tickSize(5)
    .scale(xScale);

// define the y axis
var yAxis = d3.svg.axis()
    .orient("left")
    //.ticks(12)
    .tickValues(function() {
        var timeTicks = new Array();
        for (i = 0; i < 13; i++) {
            timeTicks[i] = i * 300;
        }
        return timeTicks;
    })
    .tickFormat(function(d) {
        return formatTime(d);
    })
    .scale(yScale);



// draw y axis 
chart.append("g")
    .attr("class", "axis yaxis")
    .call(yAxis);

// draw x axis 
chart.append("g")
    .attr("class", "axis xaxis")
    .call(xAxis);

chart.append("g")
    .attr("class", "grid")
    .call(xAxis
        .tickSize(-height, 0, 0)
        .tickFormat("")
    )

chart.append("g")
    .attr("class", "grid")
    .call(yAxis
        .tickSize(-width, 0, 0)
        .tickFormat("")
    )


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
chart.append("path")
    .attr("class", "center-line")
    .attr("d", valueline([{ "time": 0, "scoreDiff": 0 }, { "time": yMax, "scoreDiff": 0 }]));
// Add halftime line
chart.append("path")
    .attr("class", "halftime-line")
    .attr("d", valueline([{ "time": yMax / 2, "scoreDiff": -nrGoals }, { "time": yMax / 2, "scoreDiff": nrGoals }]));


function populateData(data, endTime) {
    // console.log(endTime);



    scoreline
        .attr("d", valueline(filterData(data, endTime)));


    // TODO is selectAll(".point") necessary
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

    addActionTags(data);
}

function updateData(data, endTime) {
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

function hideScore() {
    // console.log('hide');
    chart.selectAll(".actionTagItem")
        .classed("hideactiontag", true);
}

var addActionTags = function(data) {

    //TODO is selectAll necessary?
    var actiontaggroup = chart.append("g").attr("id", "allactiontags")
        .selectAll(".actiontagtest").data(data)
        .enter().append("g")
        .attr("transform", function(d) {
            return "translate(" + (-195) + "," + yScale(d.time) + ")";
        })
        .attr("class", function(d) {
            return "actionTagItem icons " + getPlayerAlias(d.name) + " event-" + d.actionIndex + " " + getTeamIndicator(d.team);
        })
        .classed("hideactiontag", true);

    actiontaggroup.append("rect")
        .attr("class", "bg")
        .attr("x", 0)
        .attr("y", -8)
        .attr("width", 190)
        .attr("height", 15)
        .attr("fill", "white");


    actiontaggroup.append("text")
        .attr("class", "time")
        .attr("dx", 185)
        .attr("dy", 5)
        .text(function(d) {
            return d.timeString;
        });

    actiontaggroup.append("text")
        .attr("class", "name")
        .attr("dx", 125)
        .attr("dy", 5)
        .text(function(d) {
            return getActionText(d);
        });

    actiontaggroup.append("text")
        .attr("class", "info")
        .attr("dx", 125)
        .attr("dy", 20)
        .text(function(d) {
            return getInfoText(d);
        });

    actiontaggroup
        .filter(function(d) {
            // console.log(d.category);
            return d.category === 'yellow';
        })
        .append('rect')
        .attr("class", "card yellowcard")
        .attr("width", 10)
        .attr("height", 15)
        .attr("x", 130)
        .attr("y", -8)
        .attr("rx", 2)
        .attr("ry", 2);

    actiontaggroup
        .filter(function(d) {
            // console.log(d.category);
            return d.category === 'red';
        })
        .append('rect')
        .attr("class", "card redcard")
        .attr("width", 10)
        .attr("height", 15)
        .attr("x", 130)
        .attr("y", -8)
        .attr("rx", 2)
        .attr("ry", 2);

    var actionballs = actiontaggroup
        .filter(function(d) {
            // console.log(d.category);
            return (d.category === 'goal') || (d.category === 'penalty' && d.scoreChange);
        }).append("g")
        .attr("class", "ball")
        .attr("transform", "translate(" + 129 + "," + (-7) + "), scale(0.8)");

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
        .attr("dx", 14)
        .attr("dy", 14)
        .text("7");

    actiontaggroup
        .filter(function(d) {
            // console.log(d.category);
            return d.category === 'suspension';
        })
        .append('path')
        .attr("class", "suspension")
        .attr("transform", function(d) {
            return "translate(" + 130 + "," + -8 + ")";
        })
        .attr("d", suspensionIconPath);



}


function writeGameInfo(data) {
    
    var gameBoxTeamsY = 55;
    var gameBoxResultY = 80;
    var gameBoxOffsetX = 10;
    var gameBoxCenterX = 200;


    var gameInfoBox = chartsvg.append('g')
        .attr('id','gameInfo')
        .attr("transform", "translate(" + 30 + "," + 30 + ")")

    gameInfoBox.append('text')
        .text(data.datum + ' | ' + data.zeit + " Uhr")
        .attr("transform", "translate(" + gameBoxCenterX + "," + 10 + ")")

    gameInfoBox.append('text')
        .text(data.hallenName + ' ' + data.stadt)
        .attr("transform", "translate(" + gameBoxCenterX + "," + 30 + ")")

    gameInfoBox.append('text')
        .text(data.heim)
        .attr("class", "teams home")
        .attr("transform", "translate(" + (gameBoxCenterX-gameBoxOffsetX) + "," + gameBoxTeamsY + ")")

    gameInfoBox.append('text')
        .text('-')
        .attr("class", "teams center")
        .attr("transform", "translate(" + (gameBoxCenterX) + "," + gameBoxTeamsY + ")")

    gameInfoBox.append('text')
        .text(data.gast)
        .attr("class", "teams away")
        .attr("transform", "translate(" + (gameBoxCenterX+gameBoxOffsetX) + "," + gameBoxTeamsY + ")")

    gameInfoBox.append('text')
        .text(data.toreHeim)
        .attr("class", "result home")
        .attr("transform", "translate(" + (gameBoxCenterX-gameBoxOffsetX) + "," + gameBoxResultY + ")")

    gameInfoBox.append('text')
        .text(':')
        .attr("class", "result center")
        .attr("transform", "translate(" + (gameBoxCenterX) + "," + gameBoxResultY + ")")

    gameInfoBox.append('text')
        .text(data.toreGast)
        .attr("class", "result away")
        .attr("transform", "translate(" + (gameBoxCenterX+gameBoxOffsetX) + "," + gameBoxResultY + ")")

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
    chart.selectAll("." + className)
        .classed("hideactiontag", false);

}




var scoreboard = chartsvg.append("g")
    .attr("id", "scoreboard")
    .attr("class", "score")
    .attr("transform", "translate(" + (margin.left + (width - sbwidth) / 2) + "," + (sbtopmargin) + ")");

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


function updateScoreBoard(score) {
    scoreboard.select("#scoreHome")
        .text(score.scoreHome);
    scoreboard.select("#scoreAway")
        .text(score.scoreAway);
    scoreboard.select("#scoreTime")
        .text(score.timeString.replace(':', ' '));
}


// player profiles


var playerprofilesbox = chartsvg.append("g")
    .attr("id", "playerprofiles")
    .attr("transform", "translate(" + profilesOffset.left + "," + profilesOffset.top + ")");


 }); // end jQuery ready
