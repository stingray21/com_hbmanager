jQuery(document).ready(function($){
	//console.log('hbgyms JavaScript');
	
	var map;
	google.maps.event.addDomListener(window, 'load', initialize);
	
	
	$("select").change(function(){
		var teamkey = $(this).val();
		//console.log(teamkey);
		
		// Send a http request with AJAX http://api.jquery.com/jQuery.ajax/ 
		$.ajax({
			url: 'index.php?option=com_hbgyms&task=updateGyms&format=raw',		//the script to call to get data
			data: "teamkey=" + teamkey,				//you can insert url argumnets here to pass to api.php
			type: "POST",
			dataType:"json",
			success: function(gyms) {							//on recieve of reply
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

		$("#hallenAuswahl").html("Alle Hallen für " + $("select option:selected").html());
	});
});

function displayResult(gyms) {
	//console.log('change table content');
	var table=document.getElementById("hallenvztbl");
	
	while ( table.rows.length > 1 ) {
		table.deleteRow(1);
	}
	
	var rowlabel = 'even';
	gyms.forEach(function(element, index, array) {
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
		cellName.innerHTML=element['name'] + 
				'<br />(' + element['kurzname'] + ')';
		var cellAdr=row.insertCell(2);
		cellAdr.innerHTML= '<a href="' + link + '" target="_BLANK">'
				+ element['strasse'] + ", <br />" + element['plz'] + 
				" " + element['stadt'] + '</a>';
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
	
	addGyms(gyms);
	
}	


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
			//console.log('hbgyms JavaScript');

			map.setCenter(results[0].geometry.location);
			var marker = new google.maps.Marker({
				map: map,
				position: results[0].geometry.location,
				title: 'Schlossparkhalle',
				icon: '../media/com_hbgyms/images/marker_hb_logo.png',  
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

function addGyms(gyms) {
    var geocoder = new google.maps.Geocoder();
	for (var i = 0; i < gyms.length; ++i) {
        (function(gym) {
            var address = gym['name'] + ", " + gym['strasse'] + ", " + gym['plz'] + " " + gym['stadt'];
			//console.log(address);
			geocoder.geocode({
                'address': address
            }, function(results) {
                //console.log('=> ' + address +  ' -> ' +results);
				var marker = new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location,
                    title: address,
					icon: '../media/com_hbgyms/images/marker_hb_halle.png', 
                });

                google.maps.event.addListener(marker, 'click', function() {
                    alert(address);
                    //window.open('infomonde.php?icao=' + address + '&language=fr', 'Informations météo', config = 'height=400, width=850, toolbar=no, menubar=no, scrollbars=no, resizable=no, location=no, directories=no, status=no')
                });
				
            });
        })(gyms[i]);
		
    }
	
}