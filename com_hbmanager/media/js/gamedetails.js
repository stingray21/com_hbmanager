function importGameBtn(gameId)
{
	console.log('Import ' + gameId);
	
	importGameData(gameId, function (response) {
			// console.log(response);
			
		});
}	

function importGameData(gameId, callback)
{
	// console.log('Loading ' + gameId);
	var url = 'index.php?option=com_hbmanager&task=importGameData&format=raw';
	
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
					// console.log(httpRequest.responseText);
					var response = JSON.parse(httpRequest.responseText);
					// console.log(response);
					callback(response);
				} else {
					console.log('There was a problem with the request.');
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
	httpRequest.send('gameId=' + encodeURIComponent(gameId));
}