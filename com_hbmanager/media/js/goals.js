var log = console.log.bind(console);

var gameSelect;
var tableMode = 'sofar'; // sofar or total

document.addEventListener("DOMContentLoaded", function(event) {
	
	// log(gamesJSON);
	log(startGame);
	// log(playersJSON);
	showTableMode(tableMode);
	
	gameSelect = new Vue({
		el: '#playertable',
		data: {
			showSelectionFlag: true,
			showSelectorFlag: true,
			expanded: true,
			tempGame: startGame,
			selectedGame: startGame,
			games: gamesJSON,
			players: gamesJSON[startGame]['players']
		},
		methods: {
			selectGame: function (i) {
				// log(i);
				// log(this.games);
				if (!this.expanded) {
					this.showSelectionFlag = false;
				}
				if (this.games[i].result !== null) {
					this.selectedGame = i;
					this.tempGame = i;
					this.players = gamesJSON[i]['players'];
					document.getElementById("currentGame").getElementsByTagName("span")[0].innerHTML = this.games[i].game;
					document.getElementById("game-"+i).classList.add("selectedGame");
					showTableMode(tableMode);
					// log(this.players);
				}
			},
			showSelection: function () {
				if (!this.expanded) {
					this.showSelectionFlag = true;
				}
			},
			hideSelection: function () {
				if (!this.expanded) {
					this.showSelectionFlag = false;
				}
			},
			indicateGame: function (i) {
				//log(this.games[i].show);
				if (this.games[i].show == 1) {
					this.selectedGame = i;
				}
			},
			removeIndication: function (i) {
				//log('remove ' + this.tempGame);
				if (this.games[i].show == 1) {
					this.selectedGame = this.tempGame;
				}
			}, 
			formatDate: function(date) {
				return moment(date, 'YYYY-MM-DD').format('DD.MM.YYYY');
			},
			checkZero: function (i) {
				if (i === 0) {
					return '';
				}
				return i;
			}, 
			checkGoalie: function (i) {
				if (i !== undefined && i !== '') {
					return ' ('+i+')';
				}
				return '';
			}, 
			checkPenalty: function (penalty, played, percent) {
				if (played == 1 & penalty !== null) {
					if (percent == null) {
						return penalty;
					}
					return penalty + ' (' + percent + '%)';
				}
				return '';
			}
		}
	});

	document.getElementById("toggleSwitch").addEventListener("click", toggleTable); 
	

});	

function toggleTable () {
	var newMode = 'sofar';
	if (tableMode === 'sofar') {
		newMode = 'total';
	}
	showTableMode (newMode);
}

function showTableMode (mode) {
	var visible = 'total';
	var hidden = 'sofar';
	if (mode === 'sofar') {
		var visible = 'sofar';
		var hidden = 'total';
	}

	var els = document.getElementsByClassName(hidden);
	[].forEach.call(els, function (el) {
		el.classList.add('hidden');
	});
	els = document.getElementsByClassName(visible);
	[].forEach.call(els, function (el) {
		el.classList.remove('hidden');
	});

	tableMode = visible;
}

window.addEventListener('resize', function() {
    resizeChart();
}, false);

function resizeChart() {
		
	var width = window.innerWidth;
	log(width);
	if (width < 480) {
		gameSelect.expanded =  false;
		gameSelect.showSelectionFlag = false;
	} else {
		gameSelect.expanded =  true;
		gameSelect.showSelectionFlag = true;
	}
	log(gameSelect.expanded);
}