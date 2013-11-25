$(document).ready(function() {
	l(document.URL);
	if (document.URL.indexOf("/notificationPanel.php") >= 0) {
		$("#nPTable").dataTable({
			"bSort": false
		});
	}
});

function nPCreateNewNotification(){
	var text = $("#nPText").val();
	var link = $("#nPLink").val();
	var notificationTypeID = $("#nPNotificationTypeSelect option:selected").val();
	var activeChecked = $("#nPActive").attr("checked");

	if (activeChecked == "checked") {
		var active = 1;
	}
	else {
		var active = 0;
	}
	
	l("Start nPCreateNewNotification");
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "notification",
			mode : "createNewNotification",
			text : text,
			link : link,
			notificationTypeID : notificationTypeID,
			active: active,
		},
		success : function(result) {
			l("nPCreateNewNotification success");
			l(result);
			if (result.status !== true) {
				text = "went something wrong @ creating the Notification! - Error:" + result.status + "";
			}
			else {
				text = "successfully created the Notification!";
			}
			
			bootbox.alert(text, function() {
				window.location.reload();
			});
		}
	});
	l("End nPCreateNewNotification");
	
}


function nPEUpdateNotification(id){
	var text = $("#nPEText").val();
	var link = $("#nPELink").val();
	var notificationTypeID = $("#nPENotificationTypeSelect option:selected").val();
	var activeChecked = $("#nPEActive").attr("checked");

	if (activeChecked == "checked") {
		var active = 1;
	}
	else {
		var active = 0;
	}
	
	l("Start nPEUpdateNotification");
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "notification",
			mode : "updateNotification",
			id: id,
			text : text,
			link : link,
			notificationTypeID : notificationTypeID,
			active: active,
		},
		success : function(result) {
			l("nPEUpdateNotification success");
			l(result);
			if (result.status !== true) {
				text = "went something wrong @ updating the Notification! - Error:" + result.status + "";
			}
			else {
				text = "successfully updated Notification!";
			}
			
			bootbox.alert(text, function() {
				window.location.reload();
			});
		}
	});
	l("End nPEUpdateNotification");
}

function nPToggleActiveNotification(id, active) {
	l("Start nPToggleActiveNotification");
	
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "notification",
			mode : "toggleActiveNotification",
			id : id,
			active : active
		},
		success : function(result) {
			l("nPToggleActiveNotification success");
			l(result);
			if (result.status !== true) {
				text = "went something wrong @ updating Notification! - Error:" + result.status + "";
			}
			else {
				text = "successfully updated!";
			}
			bootbox.alert(text, function() {
				window.location.reload();
			});
		}
	});
	l("End nPToggleActiveNotification");
}

function nPEditNotification(id) {
	l("Start nPEditNotification");
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "notification",
			mode : "editNotification",
			id : id
		},
		success : function(result) {
			l("nPEditNotification success");
			l(result);
			if (result.status !== true) {
					
			}
			else {
				$("#nPEModalBody").html(result.data);
				$("#nPEditNotificationSaveChanges").attr("onclick", "nPEUpdateNotification("+result.notificationData.GlobalNotificationID+")");
				$("#nPEditNotificationModal").modal({
					backdrop : "static",
					keyboard : false
				}).css({
					width : '81%',
					'margin-left' : function() {
						return -($(this).width() / 2);
					}
				});
			}
		}
	});
	l("End nPEditNotification");
}

function nPDeleteNotification(id) {
	l("Start nPDeleteNotification");

	bootbox.confirm("Are you sure that you want to delete that Notification?", function(result) {
		if (result) {
			$.ajax({
				url : '../ajaxAdmin.php',
				type : "POST",
				dataType : 'json',
				data : {
					type : "notification",
					mode : "deleteNotification",
					id : id,
				},
				success : function(result) {
					l("nPDeleteNotification success");
					l(result);
					if (result.status !== true) {
						text = "went something wrong @ deleting Notification! - Error:" + result.status + "";
					}
					else {
						text = "successfully deleted Notification!";
					}
					bootbox.alert(text, function() {
						window.location.reload();
					});
				}
			});
			l("End nPDeleteNotification");
		}
	});
}
