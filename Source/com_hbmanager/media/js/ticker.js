"use strict";

// ============= definitions =================
var maxRuns = 250; // safety to not let it run forever

var updateInterval = 120; // interval for automatic update in seconds

var updatePause = 10; // duration for how long the update button is disabled after an update (in seconds)

var token;
var gameId;
var appid = '';
var gameInfo = {};
var extraGameInfo = {};
var gameLength = 3600;
var gameEndTimestamp = Math.floor(Date.now() / 1000) + 60 * 60 * 6;
var extraRunTime = 600; // in seconds

var event_id = 0;
var max_event_id = 0;
var eventList = [];
var playerList = [];
playerList[1] = []; // Home team

playerList[2] = []; // Away team

var timeouts = [];
timeouts[1] = []; // Home team

timeouts[2] = []; // Away team

var gameover = false;
var updateTimer; // scoreboard

var scoreboard_svg;
var scoreboard;
var sb_height = 100;
var sb_width = 150;
var sb_vert_margin = 0;
var sb_scoreline = 88;
var sb_time = 37;
var sb_home = 53;
var sb_away = 135;
var sb_radius = 8;
var sb_offset = 2; // =========== polyfill ============
// Add a getElementsByClassName function if the browser doesn't have one
// Limitation: only works with one class name
// Copyright: Eike Send http://eike.se/nd
// License: MIT License
// copied from https://gist.github.com/eikes/2299607

if (!document.getElementsByClassName) {
  document.getElementsByClassName = function (search) {
    var d = document,
        elements,
        pattern,
        i,
        results = [];

    if (d.querySelectorAll) {
      // IE8
      return d.querySelectorAll("." + search);
    }

    if (d.evaluate) {
      // IE6, IE7
      pattern = ".//*[contains(concat(' ', @class, ' '), ' " + search + " ')]";
      elements = d.evaluate(pattern, d, null, 0, null);

      while (i = elements.iterateNext()) {
        results.push(i);
      }
    } else {
      elements = d.getElementsByTagName("*");
      pattern = new RegExp("(^|\\s)" + search + "(\\s|$)");

      for (i = 0; i < elements.length; i++) {
        if (pattern.test(elements[i].className)) {
          results.push(elements[i]);
        }
      }
    }

    return results;
  };
}

if (SVGElement.prototype.getElementsByClassName === undefined) {
  SVGElement.prototype.getElementsByClassName = function (className) {
    return this.querySelectorAll('.' + className);
  };
} // ----------- END polyfill --------------------


document.addEventListener("DOMContentLoaded", function (event) {
  //console.log("DOM fully loaded and parsed");
  var urlParams = new URLSearchParams(window.location.search.substring(1));
  token = urlParams.get('token');
  gameId = urlParams.get('gameId'); // console.log(token);

  if (token === null) return false; // var base_url = 'https://spo.handball4all.de/service/if_ticker_data.php'; 
  // base_url is set in default.php of view

  if (testMode || token === 'test') {
    testMode = true;
    base_url = './media/com_hbmanager/test/ticker_feed.php';
  }

  if (testMode) {
    updateInterval = 120; // interval for automatic update in seconds

    updatePause = 2;
  } // create an svg container


  scoreboard_svg = d3.select("#scoreboardframe").append("svg:svg").attr("id", "scoreboardgraph"); // .attr("class", "hidden"); // only make visible if data loaded

  scoreboard = scoreboard_svg.append("g").attr("id", "scoreboard").attr("class", "score");
  var cmd = 'getGameInfo';
  var url = base_url + '?token=' + token + '&appid=' + appid + '&cmd=' + cmd; // console.log(url);

  d3.json(url, function (error, data) {
    if (error != null) {
      console.log(error);
    }

    ; // console.log(data);

    if (typeof data != 'undefined' || data.playerdata !== null) {
      gameInfo = data; // console.log(gameInfo);

      initializeScoreboard();
      updateGameInfo();
      now = new Date(); // console.log('Start:'+now.getHours()+':'+now.getMinutes()+':'+now.getSeconds()+' Uhr');

      runTicker();
      runUpdateTimer();
    } else {
      console.log('No data');
    }
  });
});

function updateGameInfo() {
  // console.log(gameInfo);
  var url = './index.php?option=com_hbmanager&task=getAdditionalGameInfo&format=raw&gameId=' + gameId; // console.log(url);

  var xhttp;
  xhttp = new XMLHttpRequest();

  xhttp.onreadystatechange = function () {
    document.getElementsByClassName('teams')[0].innerHTML = gameInfo.home_lname + " - " + gameInfo.guest_lname;
    document.getElementsByClassName('location')[0].innerHTML = gameInfo.gym_name + " (" + gameInfo.gym_town + ")";
    var refString = "Schiedsrichter:";
    if (gameInfo.report.refereeA.name !== null) refString += " " + gameInfo.report.refereeA.prename.substring(0, 1) + ". " + gameInfo.report.refereeA.name;
    if (gameInfo.report.refereeB.name !== null) refString += " und " + gameInfo.report.refereeB.prename.substring(0, 1) + ". " + gameInfo.report.refereeB.name;
    document.getElementsByClassName('referee')[0].innerHTML = refString;
    document.getElementById('homePlayerframe').getElementsByClassName('team')[0].innerHTML = gameInfo.home_lname;
    document.getElementById('awayPlayerframe').getElementsByClassName('team')[0].innerHTML = gameInfo.guest_lname;

    if (this.readyState == 4 && this.status == 200) {
      // console.log(this);
      // console.log(this.responseText);
      response = JSON.parse(this.responseText);

      if (response !== null) {
        extraGameInfo = response;
        document.getElementsByClassName('league')[0].innerHTML = extraGameInfo.team + ", " + extraGameInfo.league + " (" + extraGameInfo.leagueKey + ")";
        gameLength = extraGameInfo.gameLength;
      }
    }
  };

  xhttp.open("GET", url, true);
  xhttp.send();
}

function runTicker() {
  // index++;
  if (checkEndCondtion()) {
    endTicker();
  } else {
    // now = new Date();
    // console.log(now.getMinutes()+':'+now.getSeconds());
    updateTicker();
    updateTimer = setTimeout(runTicker, updateInterval * 1000);
  }
}

function checkEndCondtion() {
  if (event_id >= maxRuns) {
    console.log("zu viele Anfragen");
    var tooManyRuns = true;
  }

  if (Math.floor(Date.now() / 1000) - gameEndTimestamp > extraRunTime) {
    console.log("zu lange");
    var tooMuchTime = true;
  }

  if (tooManyRuns || tooMuchTime) {
    gameover = true;
    return true;
  }

  return false;
}

function endTicker() {
  console.log('finished');
  clearTimeout(updateTimer);
  pauseBtn(true); // console.log(eventList);
  // console.log(playerList);

  updateDisplay();
}

function updateTicker() {
  if (checkEndCondtion()) {
    endTicker();
  } else {
    getTickerCount(event_id, updateMaxEventId);
    pauseBtn();
    lastUpdate = Math.floor(Date.now() / 1000);
  }
}

function pauseBtn() {
  var _final = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;

  var btnId = 'updateTickerBtn';
  document.getElementById(btnId).disabled = true;
  document.getElementById(btnId).classList.add('disabled');

  if (!_final) {
    document.getElementById("currentEvent").classList.add('hidden');
    var loaderId = 'eventLoader';
    document.getElementById(loaderId).classList.add('run');
    setTimeout(activateBtn, updatePause * 1000);
  }
}

function activateBtn() {
  var btnId = 'updateTickerBtn';
  document.getElementById(btnId).disabled = false;
  document.getElementById(btnId).classList.remove('disabled');
}

function runUpdateTimer() {
  // console.log('runUpdateTimer');
  t = Math.floor(Date.now() / 1000);
  document.getElementById("updateTimer").innerHTML = formatTime(t - lastUpdate);

  if (event_id < maxRuns && !gameover) {
    setTimeout(runUpdateTimer, 1000);
  } else {
    document.getElementById("updateTimer").innerHTML = '-';
  }
}

function updateEvents() {
  var diff = max_event_id - event_id; // console.log(diff);

  for (i = event_id; i < max_event_id; i++) {
    // console.log(event_id,max_event_id);
    getTickerEvent(i, getEvent);
  }

  event_id = max_event_id;
}

function getTickerCount(currCount, callbackFunc) {
  // console.log(event);
  var cmd = 'getTickerCount';
  var url = base_url + '?token=' + token + '&appid=' + appid + '&cmd=' + cmd + '&index=' + currCount; // console.log(url);

  var xhttp;
  xhttp = new XMLHttpRequest();

  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      // console.log(this);
      // console.log(this.responseText);
      response = JSON.parse(this.responseText);
      callbackFunc(response);
    }
  };

  xhttp.open("GET", url, true);
  xhttp.send();
}

function updateMaxEventId(response) {
  // '{"count":43,"ticker_update":67,"status":0,"status_descripion":"OK"}'
  max_event_id = response.count;
  var loaderId = 'eventLoader';
  document.getElementById(loaderId).classList.remove('run');
  updateEvents();
}

function getTickerEvent(id, callbackFunc) {
  // console.log('getTickerEvent '+id);
  // console.log(event);
  var cmd = 'getTickerMessage';
  var url = base_url + '?token=' + token + '&appid=' + appid + '&cmd=' + cmd + '&index=' + id;
  var xhttp;
  xhttp = new XMLHttpRequest();

  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      // console.log(this.responseText);
      try {
        // console.log("Current ID:"+id);
        event = JSON.parse(this.responseText); // throw 'jsonParseException'; // generates an exception
      } catch (e) {
        // statements to handle any exceptions
        // console.log(e); // pass exception object to error handler
        console.warn('Invalid JSON');
        event = JSON.parse('{"game_time":"","home_score":"","guest_score":"","message":"","stops_time":true,"status":2,"status_descripion":"Error"}');
      }

      event.index = id; // console.log(event.game_time);
      // console.log(id, event);

      callbackFunc(event);
    }
  };

  xhttp.open("GET", url, true);
  xhttp.send();
}

function getEvent(event) {
  // console.log(event);
  event = parseEvent(event);

  if (eventList.length === max_event_id) {
    // console.log('Update eventList (List: ' + eventList.length + ' , max: ' + max_event_id + ' )' );
    var status = updateEventList();
    playerList[1] = []; // Home team

    playerList[2] = []; // Away team

    eventList.forEach(function (element) {
      if (element.status === 0 && element.player !== null && element.valid) {
        addToPlayerList(element);
      }
    });
    updateDisplay();
  }
}

function updateDisplay() {
  updatePlayerDisplay(playerList[1], 'home');
  updatePlayerDisplay(playerList[2], 'away');
  updateHistory();
  updateScoreBoard();
  document.getElementById("currentEvent").classList.remove('hidden');
  document.getElementById("currentEvent").innerHTML = eventList[0].editedMessage;
}

function updateHistory() {
  document.getElementById('historyframe').innerHTML = '';
  var table = document.createElement("TABLE");

  for (var _i = 0; _i < eventList.length; _i++) {
    if (eventList[_i].valid) {
      var row = document.createElement("TR");
      var cell;

      if (testMode) {
        cell = document.createElement("TD");
        cell.classList.add('index'); // if (!testMode) cell.classList.add('hidden');

        cell.appendChild(document.createTextNode(eventList[_i].index));
        row.appendChild(cell);
      }

      cell = document.createElement("TD");
      cell.classList.add('icon'); // console.log(eventList[i].type);

      var icon = document.createElement("SPAN");
      if (eventList[_i].type !== '') icon.classList.add(eventList[_i].type);
      cell.appendChild(icon);
      row.appendChild(cell);
      cell = document.createElement("TD");
      cell.classList.add('time');
      cell.appendChild(document.createTextNode(formatTime(eventList[_i].game_time)));
      row.appendChild(cell);
      cell = document.createElement("TD");
      cell.classList.add('message');
      cell.appendChild(document.createTextNode(eventList[_i].editedMessage));
      row.appendChild(cell);
      cell = document.createElement("TD");
      cell.classList.add('score');
      if (eventList[_i].goal) cell.classList.add('goal');
      cell.appendChild(document.createTextNode(eventList[_i].home_score));
      row.appendChild(cell);
      cell = document.createElement("TD");
      cell.classList.add('colon');
      if (eventList[_i].goal) cell.classList.add('goal');
      cell.appendChild(document.createTextNode(':'));
      row.appendChild(cell);
      cell = document.createElement("TD");
      cell.classList.add('score');
      if (eventList[_i].goal) cell.classList.add('goal');
      cell.appendChild(document.createTextNode(eventList[_i].guest_score));
      row.appendChild(cell);
      table.appendChild(row);
    }
  }

  document.getElementById('historyframe').appendChild(table);
}

function updatePlayerDisplay(list, team) {
  document.getElementById(team + 'Playerframe').getElementsByTagName('div')[0].innerHTML = '';
  var table = document.createElement("TABLE");
  var row = document.createElement("TR");
  var cell = document.createElement("TH"); // cell.appendChild(document.createTextNode('Spieler')); 

  cell.appendChild(document.createTextNode(''));
  row.appendChild(cell);
  var cell = document.createElement("TH");
  cell.appendChild(document.createTextNode('Tore'));
  row.appendChild(cell);
  var cell = document.createElement("TH");
  cell.appendChild(document.createTextNode('7m'));
  row.appendChild(cell);
  table.appendChild(row);
  list.forEach(function (d) {
    var row = document.createElement("TR");
    var cell = document.createElement("TD");
    cell.appendChild(printPlayer(d));
    row.appendChild(cell);
    var cell = document.createElement("TD");
    cell.appendChild(document.createTextNode(printGoals(d.goals)));
    row.appendChild(cell);
    var cell = document.createElement("TD");
    cell.appendChild(document.createTextNode(printGoals7m(d.goals7m, d.penalty)));
    row.appendChild(cell);
    table.appendChild(row);
  });
  document.getElementById(team + 'Playerframe').getElementsByTagName('div')[0].appendChild(table);
}

function printGoals(goals) {
  if (goals == 0) {
    return '';
  }

  return goals;
}

function printGoals7m(goals, attempts) {
  if (attempts == 0) {
    return '';
  }

  return goals + '/' + attempts;
}

function printPlayer(player) {
  var span = document.createElement("SPAN");
  var name = document.createElement("SPAN");
  var num = document.createElement("SPAN");

  if (isNaN(1 * player.player)) {
    name.appendChild(document.createTextNode('Betreuer '));
    num.appendChild(document.createTextNode(player.player));
  } else {
    name.appendChild(document.createTextNode('Spieler '));
    num.appendChild(document.createTextNode('#' + player.player));
  }

  name.classList.add('name');
  span.appendChild(name);
  span.appendChild(num);
  var cards = document.createElement("SPAN");
  cards.classList.add('icon');

  if (player['yellow'] === 1) {
    var icon = document.createElement("SPAN");
    icon.classList.add('yellow');
    cards.appendChild(icon);
  }

  for (var i = 0; i < player['suspension']; i++) {
    icon = document.createElement("SPAN");
    icon.classList.add('suspension');
    cards.appendChild(icon);
  }

  if (player['red'] === 1) {
    icon = document.createElement("SPAN");
    icon.classList.add('red');
    cards.appendChild(icon);
  }

  span.appendChild(cards); // console.log(icon);

  return span;
}

function parseEvent(event) {
  // "status_descripion":"Error"
  if (event.status_descripion !== 'OK' || event.status === 2) {
    return event;
  }

  event.game_time = event.game_time * 1;
  event.home_score = event.home_score * 1;
  event.guest_score = event.guest_score * 1; // Tor für die Heimmannschaft durch die Nummer 2
  // Mannschafts-Auszeit der Gastmannschaft
  // Verwarnung für die Nummer 17 der Gastmannschaft
  // 2-min Strafe für die Nummer 5 der Gastmannschaft
  // Spielstand 1. Halbzeit
  // Spielstand 2. Halbzeit
  // 7m-Wurf für die Gastmannschaft: Kein Treffer durch die Nummer 5
  // Erfolgreicher 7m-Wurf für die Gastmannschaft durch die Nummer 7
  // Disqualifikation für die Nummer 8 der Heimmannschaft
  // Verwarnung für die Nummer A der Heimmannschaft

  event.type = ''; // console.log(event);

  re = /^Spielstand 2. Halbzeit/i;

  if (event.message.match(re) !== null || event.game_time >= gameLength) {
    console.log("Spielende");
    gameEndTimestamp = Math.floor(Date.now() / 1000);
  }

  re = /^Tor für/i;
  event.goal = event.message.match(re) !== null ? 1 : 0;
  re = /^Erfolgreicher 7m-Wurf/i;
  event.goal7m = event.message.match(re) !== null ? 1 : 0;
  event.goal += event.goal7m;
  event.type = event.goal ? 'goal' : event.type;
  ;
  re = /^Mannschafts-Auszeit/i;
  event.type = event.message.match(re) !== null ? 'timeout' : event.type;
  ;
  re = /7m-Wurf/i;
  event.type = event.message.match(re) !== null ? 'penalty' : event.type;
  re = /^Verwarnung/i;
  event.type = event.message.match(re) !== null ? 'yellow' : event.type;
  re = /^2-min Strafe/i;
  event.type = event.message.match(re) !== null ? 'suspension' : event.type;
  re = /^Disqualifikation/i;
  event.type = event.message.match(re) !== null ? 'red' : event.type;
  event.team = 0;
  re = /Heimmannschaft/i;
  event.team = event.message.match(re) !== null ? 1 : event.team;
  re = /Gastmannschaft/i;
  event.team = event.message.match(re) !== null ? 2 : event.team;
  re = /Nummer (\d{1,2}|[A-D])/i; // TODO Mannschaftsverantw.

  temp = event.message.match(re);
  event.player = null;
  if (temp !== null) event.player = temp[1]; // console.log(event);

  event.editedMessage = event.message;
  event.editedMessage = event.editedMessage.replace(/Spielstand 1. Halbzeit/, 'Halbzeitpause');
  event.editedMessage = event.editedMessage.replace(/Spielstand 2. Halbzeit/, 'Spielende');
  event.editedMessage = event.editedMessage.replace(/(die|der) Gastmannschaft/, gameInfo.guest_lname);
  event.editedMessage = event.editedMessage.replace(/(die|der) Heimmannschaft/, gameInfo.home_lname);
  event.editedMessage = event.editedMessage.replace(/die Nummer /, '#');
  eventList.push(event);
  return event;
}

function updateEventList() {
  // eventList[event.index] = event;
  // console.log(eventList);
  eventList.sort(function (a, b) {
    // console.log(b.game_time - a.game_time);
    if (a.game_time == b.game_time) return b.index - a.index;
    return b.game_time - a.game_time;
  }); // mark duplicates

  eventList = eventList.map(function (item, pos, ary) {
    // console.log(item,ary);
    item.valid = true;

    if (pos) {
      // console.log(pos);
      var t1 = item.game_time === ary[pos - 1].game_time;
      var t2 = item.home_score === ary[pos - 1].home_score;
      var t3 = item.guest_score === ary[pos - 1].guest_score;
      var t4 = item.message === ary[pos - 1].message;
      var different = t1 * t2 * t3 * t4;
      item.valid = !different;
    }

    return item;
  }); // this might be unnecessary? ordered by game_time might be good enough?
  // eventList.sort(function(a, b) {	return (a.index < b.index);	});
  // console.log(eventList);

  return true;
}

function addToPlayerList(event) {
  if (event.type === 'timeout') {
    timeouts[event.team]++;
    return;
  }

  if (event.team === 0) return;
  var list = playerList[event.team];
  var id = list.length; // console.log(id);

  list.map(function (element, i) {
    // console.log(i, event.player, element.player);
    if (event.player === element.player) id = i;
  }); // console.log(id);

  if (id === list.length) {
    list.push({
      "player": event.player,
      "goals": 0,
      "goals7m": 0,
      "penalty": 0,
      "yellow": 0,
      "suspension": 0,
      "red": 0
    });
  } // console.log('add at '+id);


  list[id]['goals'] = list[id]['goals'] + event.goal;
  list[id]['goals7m'] = list[id]['goals7m'] + event.goal7m;

  if (event.type !== '') {
    list[id][event.type]++;
  }

  list.sort(function (a, b) {
    var p1 = isNaN(1 * a.player) ? 1000 : 1 * a.player;
    var p2 = isNaN(1 * b.player) ? 1000 : 1 * b.player; // console.log(a.player, p1, b.player, p2, p1 > p2);

    return p1 - p2;
  }); // console.log(list);
} // ========= helpers =============


function formatTime(d) {
  var min = ~~(d / 60);
  var sec = ('00' + ~~(d % 60)).substr(-2);
  return min + ":" + sec;
}

function timestring2Sec(d) {
  // console.log(d);
  var min = 1 * d.substr(-5, 2);
  var sec = 1 * d.substr(-2, 2); // console.log(min + "min and " + sec + "sec");

  time = min * 60 + sec; // console.log(time);

  return time;
} // ============= main =================


function initializeGraph() {
  console.log('initializeGraph');
}

function initializeScoreboard() {
  scoreboard_svg.attr("width", sb_width + "px").attr("height", sb_height + sb_vert_margin * 2 + "px"); // scoreboard
  // .attr("transform", "translate(" + (sb_frame_Width/2-sb_width/2) + "," + (sb_vert_margin) + ")");
  // console.log('initializeScoreboard');

  scoreboard.append("rect").attr("class", "background").attr("width", sb_width + "px").attr("height", sb_height + "px").attr("rx", sb_radius).attr("ry", sb_radius).text("0");
  scoreboard.append("rect").attr("class", "frame").attr("x", sb_offset + "px").attr("y", sb_offset + "px").attr("width", sb_width - 2 * sb_offset + "px").attr("height", sb_height - 2 * sb_offset + "px").attr("rx", sb_radius).attr("ry", sb_radius).text("0");
  scoreboard.append("rect").attr("class", "background").attr("x", 2 * sb_offset + "px").attr("y", 2 * sb_offset + "px").attr("width", sb_width - 4 * sb_offset + "px").attr("height", sb_height - 4 * sb_offset + "px").attr("rx", sb_radius).attr("ry", sb_radius).text("0");
  scoreboard.append("text").attr("class", "team").attr("x", sb_home + "px").attr("y", sb_scoreline - 32 + "px").text("HEIM");
  scoreboard.append("text").attr("class", "team").attr("x", sb_away + "px").attr("y", sb_scoreline - 32 + "px").text("GAST");
  scoreboard.append("text").attr("class", "diget time bg").attr("x", sb_width / 2 + 46 + "px").attr("y", sb_time + "px").text("88 88");
  scoreboard.append("text").attr("class", "diget dots bg").attr("x", sb_width / 2 + "px").attr("y", sb_time + "px").text(":");
  scoreboard.append("text").attr("id", "scoreTime").attr("class", "diget time").attr("x", sb_width / 2 + 46 + "px").attr("y", sb_time + "px").text(formatTime("0"));
  scoreboard.append("text").attr("class", "diget score bg").attr("x", sb_home + "px").attr("y", sb_scoreline + "px").text("88");
  scoreboard.append("text").attr("id", "scoreHome").attr("class", "diget score").attr("x", sb_home + "px").attr("y", sb_scoreline + "px").text("0");
  scoreboard.append("text").attr("class", "diget score bg").attr("x", sb_away + "px").attr("y", sb_scoreline + "px").text("88");
  scoreboard.append("text").attr("id", "scoreAway").attr("class", "diget score").attr("x", sb_away + "px").attr("y", sb_scoreline + "px").text("0");
}

function updateScoreBoard(score) {
  score = eventList[0]; // console.log('updateScoreboard');

  scoreboard.select("#scoreHome").text(score.home_score);
  scoreboard.select("#scoreAway").text(score.guest_score);
  scoreboard.select("#scoreTime").text(formatTime(score.game_time).replace(':', ' '));
}
//# sourceMappingURL=ticker.js.map
