function importGamePreview(gameId, date)
{
	console.log('Import ' + date + ' - ' + gameId);
	
	var preview = document.getElementById("import-preview");


	importGameData(gameId, date, function (gameData) {
			console.log(gameData);

			var gameInfo = gameData.gameInfo;

			var date

			var players = gameData.players;
			var actions = gameData.actions;

			preview.innerHTML = '';
			preview.innerHTML += '<h4>'+gameInfo.league+'</h4>\n';
			preview.innerHTML += '<p>'+gameInfo.gameId+'</p>\n';
			preview.innerHTML += '<p>'+gameInfo.teamHome+' - '+gameInfo.teamAway+' | '+gameInfo.result+'</p>\n';
			preview.innerHTML += '<p>'+gameInfo.date+' '+gameInfo.gym+'</p>\n';

			preview.innerHTML += '<h4>Spieler</h4>\n';

			var playerTable = '<table class="players">\n';
			playerTable += '<tr><th>Name</th><th>#</th><th></th><th>Tore</th><th>7m</th></tr>\n';
			for (var prop in players) {
				var player = players[prop];

				playerTable += '<tr>\n';
				playerTable += '<td>'+player.playerName+'</td>\n';
				playerTable += '<td>'+player.number+'</td>\n';
				playerTable += '<td>';
					if(player.goalie) playerTable += 'TW';
				playerTable += '</td>\n';
				playerTable += '<td>'+player.goals+'</td>\n';
				playerTable += '<td>';
					if (player.penalty > 0) {
						playerTable += player.penalty+'/'+(1*player.penaltyGoals);
					}
				playerTable += '</td>\n';
				// playerTable += '<td>'+player.+'</td>\n';
				playerTable += '</tr>\n';
		        // console.log(player);
		    }
			playerTable += '</table>\n\n';

			preview.innerHTML += playerTable;
			
			preview.innerHTML += '<h4>Spielverlauf</h4>\n';

			var actionTable = '<table class="actions">\n';
			actions.forEach(function(action) {
				actionTable += '<tr>\n';
				actionTable += '<td>'+action.scoreHome+':'+action.scoreAway+'</td>\n';
				actionTable += '<td>'+action.text+'</td>\n';
				actionTable += '<td>';
					if(action.playerName != null) actionTable += action.playerName;
				actionTable += '</td>\n';
				actionTable += '<td>';
					if(action.category != '') actionTable += action.category;
				actionTable += '</td>\n';
				// actionTable += '<td>'+action.+'</td>\n';
				actionTable += '</tr>\n';
		        // console.log(player);
		    });
			actionTable += '</table>\n\n';

			preview.innerHTML += actionTable;


			var button = document.getElementById("import-confirm-btn");
			button.addEventListener('click', function(){ saveGameBtn(gameInfo.gameId, gameInfo.dateUni);}, false);
			// console.log(button);

		});
}	

function importGameData(gameId, date, callback)
{
	// console.log('Loading ' + gameId);
	var url = 'index.php?option=com_hbmanager&task=previewGameData&format=raw';
	
	var httpRequest = new XMLHttpRequest();

	if (!httpRequest) {
		alert('Giving up :( Cannot create an XMLHTTP instance');
		return false;
	}
	httpRequest.onreadystatechange = function() {
		try {
			if (httpRequest.readyState === XMLHttpRequest.DONE) {
				// console.log(httpRequest.status);
				if (httpRequest.status === 200) {
					// console.log(httpRequest.responseText);
					var response = JSON.parse(httpRequest.responseText);
					 console.log(response);
					callback(response);
				} else {
					console.log('There was a problem with the request.');
				}
			}
		}
		catch( e ) {
			// alert('Caught Exception: ' + e.description);
			console.log('Caught Exception: ' + e.description);
		}
	};
	httpRequest.open('POST', url);
	httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	httpRequest.send('gameId=' + encodeURIComponent(gameId) + '&date=' + encodeURIComponent(date));
}


function saveGameBtn(gameId, date)
{
	console.log('Save ' + gameId + ' - ' + date );
	
	var row = document.getElementById("gameId_"+gameId);

	saveGameData(gameId, date, function (response) {
			// console.log(response);
			row.classList.add("hidden");
		});
}	

function saveGameData(gameId, date,  callback)
{
	console.log('Loading ' + gameId);
	var url = 'index.php?option=com_hbmanager&task=importGameData&format=raw';
	
	var httpRequest = new XMLHttpRequest();

	if (!httpRequest) {
		alert('Giving up :( Cannot create an XMLHTTP instance');
		return false;
	}
	httpRequest.onreadystatechange = function() {
		try {
			if (httpRequest.readyState === XMLHttpRequest.DONE) {
				// console.log(httpRequest.status);
				if (httpRequest.status === 200) {
					// console.log(httpRequest.responseText);
					var response = JSON.parse(httpRequest.responseText);
					// console.log(response);
					callback(response);
				} else {
					console.log('There was a problem with the request.');
				}
			}
		}
		catch( e ) {
			// alert('Caught Exception: ' + e.description);
			console.log('Caught Exception: ' + e.description);
		}
	};
	httpRequest.open('POST', url);
	httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	httpRequest.send('gameId=' + encodeURIComponent(gameId) + '&date=' + encodeURIComponent(date));
}

function showAllGames()
{
	var list = document.getElementById("importGamesList")
	var rows = Array.from(list.getElementsByTagName("tr"));
	// console.log(rows);

	rows.forEach(function(element) {
			console.log(element);
			element.classList.remove("hidden");
		})
	
}	