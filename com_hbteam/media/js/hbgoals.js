jQuery(document).ready(function($){
	
	
	$(".gamebutton").click(function(){
		console.log(this.id);
		var gameId = this.id;
		
		// TODO: only if class is not 'selected'
		
		var table = document.getElementById("scorerTable");
		var tbody = table.getElementsByTagName('tbody')[0];
		
		var season = table.getAttribute('data-season');
		var teamkey = table.getAttribute('data-teamkey');
		
		var tableGames = document.getElementById("moreGames");
		
		$.ajax({
			url:'index.php?option=com_hbteam&task=getGoals2&format=raw',
			dataType: "json",
			data: { gameId: gameId, season: season, teamkey: teamkey }, 
			success:function(data){
				//console.log(data);
				//table.innerHTML = data;
				
				//console.log(tableGames.rows);
				for (var i=0;i<tableGames.rows.length;i++)
				{ 
					if (tableGames.rows[i].id === gameId) {
						tableGames.rows[i].className = 'gamebutton selected';
					}
					else {
						tableGames.rows[i].className = 'gamebutton';
					}
				}
				
				tbody.innerHTML = '';
				
				var newRow, cellName, textName, cellGoals, textGoals,
					cellGames, textGames, cellTotal, textTotal, 
					cellRatio, textRatio, goalie;
				
				//console.log(data.player);
				data.player.forEach(function(player) 
				{
					newRow = tbody.insertRow();
					
					if (player.tw == 1 | player.twposition == 1) {
						goalie = ' (TW)';
					}
					else {
						goalie = '';
					}
					
					cellName = newRow.insertCell(0);
					cellName.className = 'name';
					textName = document.createTextNode(player.name + goalie);
					cellName.appendChild(textName);

					cellGoals = newRow.insertCell(1);
					cellGoals.className = 'goals';
					if (player.tore !== null) {
						goals = player.tore;
					}
					else {
						goals = '';
						newRow.className = 'notPlayed';
					}
					textGoals = document.createTextNode(goals);
					cellGoals.appendChild(textGoals);

					cellGames = newRow.insertCell(2);
					//cellGames.className = '';
					if (player.spiele !== null) {
						games = player.spiele;
					}
					else {
						games = '0';
					}
					textGames = document.createTextNode(games);
					cellGames.appendChild(textGames);

					cellTotal = newRow.insertCell(3);
					//cellTotal.className = '';
					if (player.toregesamt !== null) {
						goalstotal = player.toregesamt;
					}
					else {
						goalstotal = '0';
					}
					textTotal = document.createTextNode(goalstotal);
					cellTotal.appendChild(textTotal);

					cellRatio = newRow.insertCell(4);
					//cellRatio.className = '';
					if (player.quote !== null) {
						ratio = player.quote;
					}
					else {
						ratio = '0.0';
					}					
					textRatio = document.createTextNode(ratio);
					cellRatio.appendChild(textRatio);
				});
			},
			error:function(xhr,err){
				// code for error
				console.log(document.URL);
				console.log("readyState: "+xhr.readyState+"\nstatus: "+xhr.status);
				console.log("responseText: "+xhr.responseText);
			}
		});
		
	});
	
	
});

