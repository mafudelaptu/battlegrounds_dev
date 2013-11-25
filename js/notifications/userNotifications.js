/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-08-31
 */
function getDuoQueueInviteInfo(){
	l("Start getDuoQueueInviteInfo");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "QueueGroup",
			mode : "checkIfAlreadyInQueueWithGroup"
		},
		success : function(result) {
			l("getDuoQueueInviteInfo success");
			l(result);
			if (result.status !== true) {
				l("Start getJustCM");
				$.ajax({
					url : 'ajax.php',
					type : "POST",
					dataType : 'json',
					data : {
						type : "constant",
						mode : "justCM"
					},
					success : function(data) {
						l("getJustCM success");
						l(data);
						justCM = data.data;
						l("End getJustCM");	
						result.justCM = justCM;
						return result;
					}
				});
			} else {
				return false;
			}
		}
	});
	l("End getDuoQueueInviteInfo");
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function setUserNotificationAsChecked(id){
	l("Start setUserNotificationAsChecked");
	ret = $.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "userNotification",
			mode : "setUserNotificationAsChecked",
			ID : id
		}
	});
	l("End setUserNotificationAsChecked");
	return ret;
}


function sendPingNotification(that){
	var steamID = $(that).attr("data-value");
	var matchID = getParameterByName("matchID");
	
	l("SID:"+steamID+" MID:"+matchID);
	
	l("Start sendPingNotification");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "userNotification",
			mode : "sendPingNotification",
			steamID : steamID,
			matchID : matchID
		},
		success : function(result) {
			l("sendPingNotification success");
			l(result);
//			if (result.status !== true) {
//				
//			} else {
//				
//			}
		}
	});
	l("End sendPingNotification");
	
}


