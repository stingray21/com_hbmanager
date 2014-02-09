
jQuery(document).ready(function($){
	
var table=document.getElementById("hallenvztbl");
	
	// deleting data in table
	table.innerHTML = "";
	
	$(".buttonM1").click(function(){
		// Send a http request with AJAX http://api.jquery.com/jQuery.ajax/ 
		$.ajax({
			url: './components/com_hbhallenvz/test.php',		//the script to call to get data
			data: "",											//you can insert url argumnets here to pass to api.php
																//for example "id=5&parent=6"
			dataType: 'json',									//data format
			success: function(hallen) {							//on recieve of reply
				//  Update html content
				//$('#output').html("<b>id: </b>"+id+"<b> name: </b>"+vname); //Set output element html
				//recommend reading up on jquery selectors they are awesome 
				// http://api.jquery.com/category/selectors/ 
				//console.log(hallen);
				
				displayResult(hallen);
				addGyms(hallen);
			} 
		});
	});
});

function displayResult(hallen) {
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
	hallen.forEach(function addHallen(element, index, array) {
		if (rowlabel == 'even') rowlabel = 'odd';
		else rowlabel = 'even';
		
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
		cellMap.innerHtml = '<a href="https://maps.google.com/maps?saddr=Schlo%C3%9Fparkhalle%2C+Schlo%C3%9Fplatz%2C+Geislingen%2C+Deutschland&amp;daddr=72393+Burladingen+Albstra%C3%9Fe+13&amp;ie=UTF8"><img src="/handball/hb/media/com_hbhallenvz/images/google-maps-standing.png" alt="Google maps link" height="32"></a>';
		var cellTel=row.insertCell(4);
		cellTel.innerHTML=element['telefon'];
		var cellHM=row.insertCell(5);
		cellHM.innerHTML=element['haftmittel'];
	});
}

var map;
var bounds = new google.maps.LatLngBounds ();

function initialize() {
	var geocoder = new google.maps.Geocoder();
	var address = 'Schloßparkhalle, Schloßplatz 1, Geislingen, Deutschland';
	geocoder.geocode({'address': address}, function(results, status) {
		var latlngHome = results[0].geometry.location; 
		
		var mapOptions = {
			zoom: 11,
			center: latlngHome,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
		
		if (status == google.maps.GeocoderStatus.OK) {
			map.setCenter(results[0].geometry.location);
			var marker = new google.maps.Marker({
				map: map,
				position: results[0].geometry.location,
				title: 'Schlossparkhalle',
				icon: './media/com_hbhallenvz/images/marker_hb_logo.png',  
			});
		} 
		else {
			alert('Geocode was not successful for the following reason: ' + status);
		}
		
		var infoWindow = new google.maps.InfoWindow();
		
		google.maps.event.addListener(marker, 'click', function () {
				var markerContent = 'Das ist die Schlossparkhalle';
				infoWindow.setContent(markerContent);
				infoWindow.open(map, this);
			});
		});	
}

function addGyms(hallen) {
	bounds = map.getBounds();
	console.log("getbounds: " + bounds);
	
	hallen.forEach(addGym);
	console.log('gyms');	
}

function addGym(halle, index, array) {
	var geocoder = new google.maps.Geocoder();
	
	var address = halle['name'] + ", " + halle['strasse'] + ", " + halle['plz'] + " " + halle['stadt'];
	//console.log(address);
	
	geocoder.geocode( { 'address': address}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			var latlng = results[0].geometry.location; 
			//console.log(latlng);  
			var marker = new google.maps.Marker({
				map: map,
				position: results[0].geometry.location,
				title: 'test',
				icon: './media/com_hbhallenvz/images/marker_hb_halle.png',  
			});
		} 
		else {
			alert('Geocode was not successful for the following reason: ' + status);
		}

		var infoWindow = new google.maps.InfoWindow();

		google.maps.event.addListener(marker, 'click', function () {
			var markerContent = 'Das ist ein Test';
			infoWindow.setContent(markerContent);
			infoWindow.open(map, this);
		});
		
		bounds.extend (latlng);
		console.log(halle['name'] + "  test" + bounds);
		map.fitBounds (bounds);
	});	
}

//google.maps.event.addDomListener(window, 'load', initialize);