jQuery(document).ready(function($){
	
	$(".updatebutton").click(function(){
		//console.log(this.id);
		var row = $(this).parent().parent();
		row.css('background', 'yellow');
		var cellRanking = document.getElementById("ranking_"+this.id);
		var cellSchedule = document.getElementById("schedule_"+this.id);
		
		
		$.ajax({
			url:'index.php?option=com_hbmanager&task=updateTeamData&format=raw',
			type: "POST",
			dataType:"json",
			data: 'teamkey='+this.id, 
			success:function(data){
				//console.log(data);
				row.css('background', 'transparent');
				cellRanking.innerHTML=data.ranking;
				cellSchedule.innerHTML=data.schedule;
			},
			error:function(xhr,err){
				// code for error
				row.css('background', 'red');
				console.log(document.URL);
				console.log("readyState: "+xhr.readyState+"\nstatus: "+xhr.status);
				console.log("responseText: "+xhr.responseText);
			}
		});
		
	});
	
	$("#hvwupdateall").click(function(){
		console.log('test');
		
		
		$.ajax({
			url:'index.php?option=com_hbmanager&task=getHvwTeams&format=raw',
			type: "POST",
			dataType:"json",
			success:function(data){
				//console.log(data);
				
				var index;
				var cellRanking = new Array();
				var cellSchedule = new Array();
				var row = new Array();
				var teamkey = new Array();
				//var a = ["a", "b", "c"];
				for (index = 0; index < data.length; ++index) {
					teamkey[index] = data[index];
					console.log(teamkey[index]);
					cellRanking[index] = document.getElementById("ranking_"+teamkey[index]);
					cellSchedule[index] = document.getElementById("schedule_"+teamkey[index]);
					row[index] = cellSchedule.parentElement;
					row.bgColor="yellow";
				
					$.ajax({
						url:'index.php?option=com_hbmanager&task=updateTeamData&format=raw',
						type: "POST",
						dataType:"json",
						data: 'teamkey='+teamkey[index], 
						success:function(data){
							//console.log(data);
							row[index].bgColor="transparent";
							cellRanking[index].innerHTML=data.ranking;
							cellSchedule[index].innerHTML=data.schedule;
						},
						error:function(xhr,err){
							// code for error
							row[index].bgColor="red";
							console.log(document.URL);
							console.log("readyState: "+xhr.readyState+"\nstatus: "+xhr.status);
							console.log("responseText: "+xhr.responseText);
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

