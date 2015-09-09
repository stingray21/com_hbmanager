jQuery(document).ready(function($){
	
	console.log('Update teams');
		
	var start = performance.now();
	
	$.ajax({
		url:'index.php?option=com_hbmanager&task=getOutdatedTeams&format=raw',
		type: "POST",
		dataType:"json",
		success:function(teams){
			//console.log(teams);
			var index, teamkey;
			if (teams == null) {
				document.getElementById("eggtimer").style.visibility = 'hidden';
				document.getElementById("hvwupdate").innerHTML += 
								"<p><b>kein UPDATE</b></p>";
			}
			else {
				for (var index = 0; index < teams.length; ++index) {
					teamkey = teams[index].kuerzel;
					console.log(teamkey);

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
							var end = performance.now();
							var time = Math.round(end - start ) / 1000;
							document.getElementById("hvwupdate").innerHTML += 
									"<p><b>UPDATE abgeschlossen</b> (" + 
									time + " sec)</p>";
						}
					});
				}
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

function updateTeamData($, teamkey, teams, callback)
{
	console.log('update ' + teamkey);
	
	$.ajax({
		url:'index.php?option=com_hbmanager&task=updateTeamData&format=raw',
		type: "POST",
		dataType:"json",
		data: 'teamkey='+teamkey, 
		success:function(data){
			//console.log(data);
			document.getElementById("hvwupdate").innerHTML += "<p><b>" 
					+ teamkey + "</b><br/>" + 
					"Tabelle aktualisiert (" + data.ranking + ") <br/>" +
					"Spielplan aktualisiert (" + data.schedule + ") </p>";
			callback(teams, teamkey);
		},
		error:function(xhr,err){
			// code for error
			document.getElementById("hvwupdate").innerHTML += "<p><b>" 
					+ teamkey + "</b><br/>" + 
					"ERROR </p>";
			console.log(document.URL);
			console.log("readyState: "+xhr.readyState+"\nstatus: "+xhr.status);
			console.log("responseText: "+xhr.responseText);
		}
	});
}

