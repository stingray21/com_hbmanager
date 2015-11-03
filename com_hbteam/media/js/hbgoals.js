var log = console.log.bind(console);

//var gameSelect;

jQuery(document).ready(function($){
	
	//log(gamesJSON);
	
	var gameSelect = new Vue({
		el: '#playertable',
		data: {
			showSelectionFlag: true,
			showSelectorFlag: true,
			expanded: true,
			tempGame: startGame,
			selectedGame: startGame,
			games: gamesJSON,
			players: playersJSON
		},
		methods: {
			selectGame: function (i) {
				//log(this.games);
				if (!this.expanded) {
					this.showSelectionFlag = false;
				}
				
				if (this.games[i].show == 1) {
					this.selectedGame = i;
					this.tempGame = i;
					//log(this.games[i]);
					loadData(this.games[i]);
					//log(this.players);
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
			}
		}
	});

	function loadData ( game ) {
		
		//log("loadData", id);
		$.getJSON("./index.php?option=com_hbteam&task=getGoalsJSON&format=raw" 
				+ "&teamkey=" + game.teamkey 
				+ "&season=" + game.season 
				+ "&gameId=" + game.id,
			function(data) {
				//log(data);
				gameSelect.players =  data;
		});
	}
	
	$( window ).resize(function() {
		
		var width = $( window ).width();
		log(width);
		if (width < 480) {
			gameSelect.expanded =  false;
			gameSelect.showSelectionFlag = false;
		} else {
			gameSelect.expanded =  true;
			gameSelect.showSelectionFlag = true;
		}
		log(gameSelect.expanded);
	});
	
});	

