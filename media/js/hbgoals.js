jQuery(document).ready(function($){
	
	
	$(".gamebutton").click(function(){
		//console.log(this.id);
		var gameId = this.id;
		var table = document.getElementById("scorerTable");
		var tbody = table.getElementsByTagName('tbody')[0];
		
		var season = table.getAttribute('data-season');
		var teamkey = table.getAttribute('data-teamkey');
		
		var tableGames = document.getElementById("moreGames");
		
		$.ajax({
			url:'index.php?option=com_hbteamhome&task=getGoals2&format=raw',
			dataType: "json",
			data: { gameId: gameId, season: season, teamkey: teamkey }, 
			success:function(data){
				//console.log(data);
				//table.innerHTML = data;
				
				//console.log(tableGames.rows);
				for (var i=0;i<tableGames.rows.length;i++)
				{ 
					if (tableGames.rows[i].className !== '')
					{
						if (tableGames.rows[i].id === gameId) {
							tableGames.rows[i].className = 'selected';
						}
						else {
							tableGames.rows[i].className = 'gamebutton';
						}
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
					
					if (player.tw == 1) {
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
					textGoals = document.createTextNode(player.tore);
					cellGoals.appendChild(textGoals);

					cellGames = newRow.insertCell(2);
					//cellGames.className = '';
					textGames = document.createTextNode(player.spiele);
					cellGames.appendChild(textGames);

					cellTotal = newRow.insertCell(3);
					//cellTotal.className = '';
					textTotal = document.createTextNode(player.toregesamt);
					cellTotal.appendChild(textTotal);

					cellRatio = newRow.insertCell(4);
					//cellRatio.className = '';
					textRatio = document.createTextNode(player.quote);
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

