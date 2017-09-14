jQuery(document).ready(function($){
	$(".btnShowGameDay").click(function(){
        console.log("Show Game Day");
		var btnId = this.parentElement.id;
		console.log(btnId);
		var key = btnId.replace("tag", "table");
		// console.log(key);
        var bullet = this.parentElement.getElementsByClassName('daybullet')[0];
        // console.log(bullet);
        var currTable = document.getElementById(key);
		// console.log(currTable);
		if (currTable.getAttribute('data-state') === 'hidden') {
            currTable.setAttribute('data-state', 'visible');
          $('#'+key).fadeIn();
            bullet.className = "daybullet arrow-down";
            //this.innerHTML = 'Tabelle ausblenden';
            //this.style.background = '#04c'; 
            //this.style.color = '#fff';
        }
        else {
            currTable.setAttribute('data-state', 'hidden');
			$('#'+key).fadeOut();
            bullet.className = "daybullet arrow-right";
            //this.innerHTML = 'Tabelle einblenden';
            //this.style.background = 'transparent'; 
            //this.style.color = '#000';
        }
	});
});