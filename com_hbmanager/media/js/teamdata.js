function updateTeamBtn(teamkey)
{
	//console.log('Update ' + teamkey);
	var row = document.getElementById("update-team-"+teamkey);

	row.getElementsByClassName("indicator")[0].classList.remove("show");
	row.getElementsByClassName("details")[0].classList.remove("show");
	row.getElementsByClassName("updateBtn")[0].classList.add("spinner");
	
	updateTeamData(teamkey, function (response) {
			// console.log(response);
			
			row.getElementsByClassName("updateBtn")[0].classList.remove("spinner");
			row.getElementsByClassName("date")[0].innerHTML = response.date;
			
			row.getElementsByClassName("updateStatus")[0].classList.add("show");
			 
			if (response.result.total == true) 
			{
				row.getElementsByClassName("indicator")[0].classList.add("icon-checkmark", "show");
			} 
			else 
			{
				row.getElementsByClassName("details")[0].classList.add("show");

				var classSchedule = ((response.result.schedule == true) ? "icon-checkmark" : "icon-warning");
				row.getElementsByClassName("schedule")[0].getElementsByTagName("span")[0].classList.add(classSchedule);

				var classStandings = ((response.result.standings == true) ? "icon-checkmark" : "icon-warning");
				row.getElementsByClassName("standings")[0].getElementsByTagName("span")[0].classList.add(classStandings);

				var classStandingsDetails = ((response.result.standingsDetails == true) ? "icon-checkmark" : "icon-warning");
				row.getElementsByClassName("standings-details")[0].getElementsByTagName("span")[0].classList.add(classStandingsDetails);
			}
		});

}	

function updateTeams(teams)
{
	teams.forEach(function(team) {
		// console.log(element);
		updateTeamBtn(team.teamkey);
	});

}	

function updateTeamBtn(teamkey)
{
	//console.log('Update ' + teamkey);
	var row = document.getElementById("update-team-"+teamkey);

	row.getElementsByClassName("indicator")[0].classList.remove("show");
	row.getElementsByClassName("details")[0].classList.remove("show");
	row.getElementsByClassName("updateBtn")[0].classList.add("spinner");
	
	updateTeamData(teamkey, function (response) {
			// console.log(response);
			
			row.getElementsByClassName("updateBtn")[0].classList.remove("spinner");
			row.getElementsByClassName("date")[0].innerHTML = response.date;
			
			row.getElementsByClassName("updateStatus")[0].classList.add("show");
			 
			if (response.result.total == true) 
			{
				row.getElementsByClassName("indicator")[0].classList.add("icon-checkmark", "show");
			} 
			else 
			{
				row.getElementsByClassName("details")[0].classList.add("show");

				var classSchedule = ((response.result.schedule == true) ? "icon-checkmark" : "icon-warning");
				row.getElementsByClassName("schedule")[0].getElementsByTagName("span")[0].classList.add(classSchedule);

				var classStandings = ((response.result.standings == true) ? "icon-checkmark" : "icon-warning");
				row.getElementsByClassName("standings")[0].getElementsByTagName("span")[0].classList.add(classStandings);

				var classStandingsDetails = ((response.result.standingsDetails == true) ? "icon-checkmark" : "icon-warning");
				row.getElementsByClassName("standings-details")[0].getElementsByTagName("span")[0].classList.add(classStandingsDetails);
			}
		});

}	

function updateTeamData(teamkey, callback)
{
	// console.log('Loading ' + teamkey);
	var url = 'index.php?option=com_hbmanager&task=updateTeamData&format=raw';
	
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
					var response = JSON.parse(httpRequest.responseText);
					console.log(response.teamkey + ': ' + response.date);
					// console.log(response.result);
					callback(response);
				} else {
					console.log('There was a problem with the request.');
					var response = {"result": {"total": false}};
					// console.log(response.result);										
					callback(response);
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
	httpRequest.send('teamkey=' + encodeURIComponent(teamkey));
}

function updateCheckedTeams()
{
	var checkedBoxes = document.querySelectorAll('input[type=checkbox]:checked');
	// console.log(checkedBoxes);

	var teams = [];
	for (var i=0; i<checkedBoxes.length; i++) {
		if (checkedBoxes[i].name != "checkall-toggle") 
		{
			var teamkey = checkedBoxes[i].parentElement.parentElement.id;
			teamkey = teamkey.replace('update-team-', '');

			teams.push({"teamkey": teamkey, "update": null});
			// teams.push({"teamkey": "M-1", "update": "2017-10-04 19:37:28"});
		}
    }
    // console.log(teamkeys);
	
	return updateTeams(teams);
}

function updateAllTeams()
{
	var checkedBoxes = document.querySelectorAll('input[type=checkbox]');
	// console.log(checkedBoxes);

	var teams = [];
	for (var i=0; i<checkedBoxes.length; i++) {
		if (checkedBoxes[i].name != "checkall-toggle") 
		{
			var teamkey = checkedBoxes[i].parentElement.parentElement.id;
			teamkey = teamkey.replace('update-team-', '');

			teams.push({"teamkey": teamkey, "update": null});
			// teams.push({"teamkey": "M-1", "update": "2017-10-04 19:37:28"});
		}
    }
    // console.log(teamkeys);
	
	return updateTeams(teams);
}