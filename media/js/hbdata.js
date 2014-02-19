jQuery(document).ready(function($){
	
	$(".updatebutton").click(function(){
		//console.log(this.id);
		var teamkey = this.id;
		var teams = null;
		
		document.getElementById("eggtimer").style.visibility = 'visible';
		
		updateTeamData($, teamkey, teams, function (teams, teamkey) {
			document.getElementById("eggtimer").style.visibility = 'hidden';
		});
	});
	
	$("#hvwupdateall").click(function(){
		console.log('Update all teams');
		
		document.getElementById("eggtimer").style.visibility = 'visible';
		
		$.ajax({
			url:'index.php?option=com_hbmanager&task=getHvwTeams&format=raw',
			type: "POST",
			dataType:"json",
			success:function(teams){
				//console.log(teams);
				var index, teamkey;
				
				for (var index = 0; index < teams.length; ++index) {
					teamkey = teams[index].kuerzel;
					//console.log(teamkey);
					
					updateTeamData($, teamkey, teams, function (teams, teamkey) {
						var checked = 0;
						for (var index = 0; index < teams.length; ++index) {
							if (teamkey === teams[index].kuerzel){
								teams[index].updated = true;
								//console.log(teamkey);
							}
							if (teams[index].updated === true) {
								checked++;
							}

						}
						//console.log('No. teams:' + teams.length + ' No updated' + checked );
						//console.log(teams);
						if (teams.length === checked) {
							document.getElementById("eggtimer").style.visibility = 'hidden';
						}
					});
				}
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

function updateTeamData($, teamkey, teams, callback)
{
	console.log('update ' + teamkey);
	
	var cellRanking = new Array();
	var cellSchedule = new Array();
	var row = new Array();
	cellRanking[teamkey] = document.getElementById("ranking_"+teamkey);
	cellSchedule[teamkey] = document.getElementById("schedule_"+teamkey);
	row[teamkey] = cellSchedule[teamkey].parentElement;
	row[teamkey].bgColor="yellow";
	
	$.ajax({
		url:'index.php?option=com_hbmanager&task=updateTeamData&format=raw',
		type: "POST",
		dataType:"json",
		data: 'teamkey='+teamkey, 
		success:function(data){
			//console.log(data);
			row[teamkey].bgColor="transparent";
			cellRanking[teamkey].innerHTML=data.ranking;
			cellSchedule[teamkey].innerHTML=data.schedule;
			callback(teams, teamkey);
		},
		error:function(xhr,err){
			// code for error
			row[teamkey].bgColor="red";
			console.log(document.URL);
			console.log("readyState: "+xhr.readyState+"\nstatus: "+xhr.status);
			console.log("responseText: "+xhr.responseText);
		}
	});
}

