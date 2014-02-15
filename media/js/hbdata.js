jQuery(document).ready(function($){
	
	$(".updatebutton").click(function(){
		//console.log(this.id);
		var teamkey = this.id;
		
		updateTeamData($, teamkey);
	});
	
	$("#hvwupdateall").click(function(){
		console.log('Update all teams');
		
		$.ajax({
			url:'index.php?option=com_hbmanager&task=getHvwTeams&format=raw',
			type: "POST",
			dataType:"json",
			success:function(data){
				//console.log(data);
				var index, teamkey;
				
				for (index = 0; index < data.length; ++index) {
					teamkey = data[index];
					console.log(teamkey);
					
					updateTeamData($, teamkey);
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

function updateTeamData($, teamkey)
{
	console.log('test in function');
	
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