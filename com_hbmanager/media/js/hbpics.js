var log = console.log.bind(console);

jQuery(document).ready(function($){
	
	var vm = new Vue({
		el: "#playertable",
		data: {
		  players: []
		},
		ready: function () {
		  vm = this;
		  loadData(70411);
		},
		methods: {
		  update: function ( id ) {
			log("update", id);
			loadData(id);
		  }
		}
	});

	function loadData ( id ) {
		log("loadData", id);
		$.getJSON("http://localhost/handball/hb_joomla3/index.php?option=com_hbteam&task=getGoals2&format=raw&gameId=" + id + "&season=2014&teamkey=m-1", function(data) {
			log(data);
			vm.players = data.player;
		});
	}

	function loadData2 ( id ) {
		log("loadData2", id);
		$.ajax({
			url:'index.php?option=com_hbteam&task=getGoals2&format=raw',
			dataType: "json",
			data: { gameId: gameId, season: season, teamkey: teamkey }, 
			success:function(data){
				log(data);
				vm.players = data.player;
			},
			error:function(xhr,err){
				// code for error
				console.log(document.URL);
				console.log("readyState: "+xhr.readyState+"\nstatus: "+xhr.status);
				console.log("responseText: "+xhr.responseText);
			}
		});
	}

	
});