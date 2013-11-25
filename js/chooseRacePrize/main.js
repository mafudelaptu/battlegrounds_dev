/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
$(document).ready(function() {
	if (document.URL.indexOf("/chooseRacePrize.php") >= 0) {
		
}
});
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function saveChoosePrizeSelection(){
	l("saveChoosePrizeSelection Start");
	// Item Selection auslesen
	var itemID = $("input[type='radio']:checked").val();
	var raceID = getParameterByName("rID");
	if(typeof itemID !='undefined'){
		
		$.ajax({
			url : 'ajax.php',
			type : "POST",
			dataType : 'json',
			data : {
				type : "RaceWinner",
				mode : "setSelectedPrize",
				ID: itemID,
				raceID: raceID
			},
			success : function(result) {
				l("saveChoosePrizeSelection success");
				l(result);
				var loc = "index.php";
				switch(result.prizeTypeID){
					case "6":
						text = "<div align='center'><h4>You successfully choose your Prize</h4><p>Please check your profile whether the selected prize was also really properly transferred.</p>";
						loc = "profile.php#bp";
						break;
					default:
						text = "<div align='center'><h4>You successfully choose your Prize</h4> "
							+ "<p>In near future you should get a friend-request of 'Dota2LeaguePrizes' and will trade your Prize!</p>";
						
					
				}
				bootbox.alert(text, function() {
				window.location = loc;
			});
			}
		});
		
	}
	else{
		alert("you have to choose a prize first!");
	}
	l("saveChoosePrizeSelection End");
}