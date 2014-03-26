jQuery(document).ready(function($){
	
	
	$(".gamebutton").click(function(){
		//console.log(this.id);
		var gameId = this.id;
		var table = document.getElementById("scorerTable");
		
		var season = table.getAttribute('data-season');
		var teamkey = table.getAttribute('data-teamkey');
		
		$.ajax({
			url:'index.php?option=com_hbteamhome&task=getGoals&format=raw',
			type:'POST',
			data: { gameId: gameId, season: season, teamkey: teamkey }, 
			success:function(data){
				//console.log(data);
				table.innerHTML = data;
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

