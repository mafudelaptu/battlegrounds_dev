$(document).ready(
		function() {
			l(document.URL);
			if (document.URL.indexOf("/matchResultPanel.php") >= 0) {
				
			}
		});



function mRShowMatchResults(){
	var matchID = $("#mRMatchID").val();
	
	window.location.href = "editMatchResult.php?matchID="+parseInt(matchID);
}