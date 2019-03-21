jQuery(document).ready(function($){
	$(".btnShowGameDay").click(function(){
        console.log("Show Game Day");
		var btnId = this.parentElement.id;
		console.log(btnId);
        var btnClass = this.parentElement.className;
        console.log(btnClass);
		if (btnClass.includes("showDay")) {
            //this.parentElement.removeClass = "gamedaytag";
            $('#'+btnId).removeClass("showDay");
        } else {
            // this.parentElement.className = "gamedaytag showDay";
            $('#'+btnId).addClass("showDay");
        }
	});

    $('.gymButton').click(function(){
        var gymId = this.id.replace('buttonGym','');
        console.log("Show "+gymId);
        var gymClass = this.className;
        if (gymClass.includes("hideGym")) {
            $('#buttonGym'+gymId).removeClass("hideGym");
            $(".gym"+gymId).removeClass("hiddenGym");
        } else {
            $('#buttonGym'+gymId).addClass("hideGym");
            $(".gym"+gymId).addClass("hiddenGym");
        }
        
    });
});