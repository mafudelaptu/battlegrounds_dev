/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
$(document).ready(
		function() {
			url = document.URL;
			if (url.indexOf("/event.php") >= 0){
				$(".team").popover({
					html:true,
					placement: "bottom"
				});
				
				initChat("event", parseInt(getParameterByName("eventID"))+"_"+parseInt(getParameterByName("cEID")), "eventChat"); /* Start the inital request */
				
				// team1 f�llen
				$("#team1Box").attr("data-content", $("#team1DataHTML").html());
				// team2 f�llen
				$("#team2Box").attr("data-content", $("#team2DataHTML").html());
				// team3 f�llen
				$("#team3Box").attr("data-content", $("#team3DataHTML").html());
				// team4 f�llen
				$("#team4Box").attr("data-content", $("#team4DataHTML").html());
				// team5 f�llen
				$("#team5Box").attr("data-content", $("#team5DataHTML").html());
				// team6 f�llen
				$("#team6Box").attr("data-content", $("#team6DataHTML").html());
				
				//initGracket();
			}
		});
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function initGracket(){
	$("div.g_team.t1").attr("data-content", $("#team1DataHTML").html());
	$("#dataGracket").gracket({
		 roundLabels : ["1st Round", "2nd Round", "Winner"],
		 src : [
		          [
		            [ {"name" : "Team 1", "id" : "t1"}, {"name" : "Team 2", "id" : "t2"} ],
		            [ {"name" : "Team 3", "id" : "t3"}, {"name" : "Team 4", "id" : "t4"} ]
		          ],
		          [
		            [ {"name" : "Team 5", "id" : "t5"}, {"name" : "Team 6", "id" : "t6"} ]
		          ],
		          [
		            [ {"name" : "Team 5 or Team 6", "id" : "t7"} ]
		          ]
		        ]
	});
	
}