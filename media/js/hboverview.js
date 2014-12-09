jQuery(document).ready(function($){
	$(".btnShowTable").click(function(){
		var btnId = this.id;
		//console.log(btnId);
		var key = btnId.replace("btnShow", "standings");
        var currTable = document.getElementById(key);
		//console.log(key);
		if (currTable.getAttribute('data-state') === 'hidden') {
            currTable.setAttribute('data-state', 'visible');
            $('#'+key).fadeIn();
            //this.innerHTML = 'Tabelle ausblenden';
            //this.style.background = '#04c'; 
            //this.style.color = '#fff';
        }
        else {
            currTable.setAttribute('data-state', 'hidden');
            $('#'+key).fadeOut();
            //this.innerHTML = 'Tabelle einblenden';
            //this.style.background = 'transparent'; 
            //this.style.color = '#000';
        }
	});
});