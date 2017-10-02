function updateTeamBtn(teamkey)
{
	console.log('Update ' + teamkey);

	document.getElementById("update-team-"+teamkey).classList.add("spinner");
	
	updateTeamData(teamkey, function (response) {
			document.getElementById("update-team-"+teamkey).classList.remove("spinner");
			document.getElementById("date-"+teamkey).innerHTML = response.date;
		});

}	

function updateTeamData(teamkey, callback)
{
	var url = 'index.php?option=com_hbmanager&task=updateTeamData&format=raw';
	
	httpRequest = new XMLHttpRequest();

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
