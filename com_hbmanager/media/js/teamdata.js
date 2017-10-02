function updateTeamBtn(teamkey)
{
	console.log('Update ' + teamkey);

	document.getElementById("update-team-"+teamkey).getElementsByClassName("updateBtn")[0].classList.add("spinner");
	
	updateTeamData(teamkey, function (response) {
			document.getElementById("update-team-"+teamkey).getElementsByClassName("updateBtn")[0].classList.remove("spinner");
			document.getElementById("update-team-"+teamkey).getElementsByClassName("date")[0].innerHTML = response.date;
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
					callback(response);
				} else {
					console.log('There was a problem with the request.');
				}
			}
		}
		catch( e ) {
			alert('Caught Exception: ' + e.description);
		}
	};
	httpRequest.open('POST', url);
	httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	httpRequest.send('teamkey=' + encodeURIComponent(teamkey));
}

function updateTeams()
{
	var checkedBoxes = document.querySelectorAll('input[type=checkbox]:checked');
	// console.log(checkedBoxes);

	var teamkeys = [];
	for (var i=0; i<checkedBoxes.length; i++) {
		if (checkedBoxes[i].name != "checkall-toggle") 
		{
			var teamkey = checkedBoxes[i].parentElement.parentElement.id;
			teamkey = teamkey.replace('update-team-', '');

			updateTeamBtn(teamkey);

			teamkeys.push(teamkey);
		}
    }
    // console.log(teamkeys);
	
	return true;
}