/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function setEventReadyStatus(eventID, status){
	l("setEventReadyStatus Start");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "EventSubmissions",
			mode : "setReadyStatus",
			event : eventID,
			status: status
		},
		success : function(result) {
			l("setEventReadyStatus success");
			l(result);
			$("#readyForEvent").modal("hide");
			if (result.status) {
				window.location.reload();
			}
			
		}
	});
	l("setEventReadyStatus End");
}