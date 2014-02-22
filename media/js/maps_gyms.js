jQuery(document).ready(function($){
	
    $("select").change(function(){
        var teamkey = $(this).val();
        //console.log(teamkey);
		
		// Send a http request with AJAX http://api.jquery.com/jQuery.ajax/ 
		$.ajax({
			url: 'index.php?option=com_hbgyms&task=updateGyms&format=raw',		//the script to call to get data
			data: "teamkey=" + teamkey,				//you can insert url argumnets here to pass to api.php
																//for example "id=5&parent=6"
			dataType: 'json',									//data format
			success: function(gyms) {							//on recieve of reply
				//  Update html content
				//$('#output').html("<b>id: </b>"+id+"<b> name: </b>"+vname); //Set output element html
				//recommend reading up on jquery selectors they are awesome 
				// http://api.jquery.com/category/selectors/ 
				//console.log(gyms);
				displayResult(gyms);
			}			,
			error:function(xhr,err){
				// code for error
				console.log(document.URL);
				console.log("readyState: "+xhr.readyState+"\nstatus: "+xhr.status);
				console.log("responseText: "+xhr.responseText);
			}
		});

        $("#hallenAuswahl").html("Alle Hallen, in denen die " + $("select option:selected").html() + " diese Saison spielt");
	});
});

function displayResult(gyms) {
	var table=document.getElementById("hallenvztbl");
	
	// deleting data in table
	table.innerHTML = "";
	
	// create header
	if (!table.tHead) {
		var header=table.createTHead();
		var row=header.insertRow(0);
		
		var cellNr = row.appendChild(document.createElement("th"));
		cellNr.innerHTML="Nr";
		var cellName = row.appendChild(document.createElement("th"));
		cellName.innerHTML="Name";
		var cellAdr = row.appendChild(document.createElement("th"));
		cellAdr.innerHTML="Adresse";
		cellAdr.className="link";
		var cellMap = row.appendChild(document.createElement("th"));
		cellMap.innerHTML="";
		cellMap.className="map";
		var cellTel = row.appendChild(document.createElement("th"));
		cellTel.innerHTML="Telefon";
		var cellHM = row.appendChild(document.createElement("th"));
		cellHM.innerHTML="Haftmittel";
		//d.className = d.className + " otherclass";
	}
	
	/*
	
	// Test Array
	var halle = {};   // {} is a shortcut for "new Object()"

	halle['id'] = "3"; 
	halle['hallenNummer'] = "7004"; 
	halle['kurzname'] = "Balingen 2"; 
	halle['name'] = "L\xE4ngenfeldhalle"; 
	halle['plz'] = "72336";
	halle['stadt'] = "Balingen";
	halle['strasse'] = "Gymnasiumstra\xDFe 32"; 
	halle['telefon'] = "07433-900036";
	halle['haftmittel'] = "Eingeschr\xE4nktes Haftmittelverbot: wasserl\xF6sliche Haftmittel erlaubt";
	
	
	for(key in halle){
		// for-in loop goes over all properties including inherited properties
		// let's use only our own properties
		if(halle.hasOwnProperty(key)){
			console.log(key + ": " + halle[key]);
		}
	}

	var hallen = new Array(halle, halle, halle);
	console.log(hallen);
	//*/
	
	
	
	var rowlabel = 'even';
	gyms.forEach(function addHallen(element, index, array) {
		if (rowlabel == 'even') rowlabel = 'odd';
		else rowlabel = 'even';
		
		var start = 'Schloßparkhalle, Schloßplatz, Geislingen, Deutschland';
		var destination = element['plz'] + ' ' + element['stadt'] + ' ' +  element['strasse'];
		var link = 'https://maps.google.com/maps?saddr=' + encodeURIComponent(start) + '&daddr=' + encodeURIComponent(destination) + '&ie=UTF8';
		
		
		var row=table.insertRow(-1);
		row.className=rowlabel;
		var cellNr=row.insertCell(0);
		cellNr.innerHTML=element['hallenNummer'];
		var cellName=row.insertCell(1);
		cellName.innerHTML=element['kurzname'];
		var cellAdr=row.insertCell(2);
		cellAdr.innerHTML=element['strasse'] + ", <br />" + element['plz'] + " " + element['stadt'];
		cellAdr.className="link";
		var cellMap=row.insertCell(3);
		//cellMap.innerHTML= "Map";
		cellMap.className="map";
		cellMap.innerHTML = '<a href="' + link + '" target="_BLANK"><span class="google-map-icon"></span></a>';
		var cellTel=row.insertCell(4);
		cellTel.innerHTML=element['telefon'];
		var cellHM=row.insertCell(5);
		cellHM.innerHTML=element['haftmittel'];
	});
}

