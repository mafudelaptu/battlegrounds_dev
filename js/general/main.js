/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
$(document).ready(function() {
	if (document.URL.indexOf("/admin/") == -1) {
		initGetNotifications();
		//getNotifcations();
		
	}
	else{
		//alert(document.URL.indexOf("/admin/"));
	}
	
});

/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function initGetNotifications() {
	l("Start initGetNotifications");
	notificationUpdate(); /* Start the inital request */
	l("END initGetNotifications");
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function notificationUpdate() {
	l("Start notificationUpdate");

	$.ajax({
		type : "POST",
		url : "longPolling/notification.php",
		async : true,
		dataType : 'json',
		// data : {},
		cache : true,
		timeout : 20000, /* Timeout in ms */

		success : function(data) {
			l("success - notificationUpdate");
			l(data);

			if (typeof data != "undefined" && data != null) {
				if (data.status) {
					l("antwort juhu!");
					handleNotifications(data);
				}
				else {
					setTimeout(function() {
						notificationUpdate();
					}, 1000);
				}
			}
			else {
				setTimeout(function() {
					notificationUpdate();
				}, 1000);
			}
		},
		error : function(XMLHttpRequest, textStatus, errorThrown) {
			// addmsg("error", textStatus + " (" + errorThrown + ")");
			setTimeout(function() {
				l("error:" + errorThrown);
				notificationUpdate();
			}, 15000); /* milliseconds (15seconds) */
		}
	});
	l("END notificationUpdate");
};
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function handleNotifications(data) {
	var type = data.type;
	var userNotID = data.data.UserNotificationID;
	l("Type:" + type);
	switch (type) {
		case "user":
			l("hier!!!");
			var notTypeID = data.data.NotificationTypeID;
			l(notTypeID);
			switch (notTypeID) {
				case "1":
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
							if (result.status == true) {
								var inviteData = null;
								l("Start getJustCM");
								$.ajax({
									url : 'ajax.php',
									type : "POST",
									dataType : 'json',
									data : {
										type : "constant",
										mode : "justCM"
									},
									success : function(justCM) {
										l("getJustCM success");
										l(data);
										justCM = justCM.data;
										l("End getJustCM");
										result.justCM = justCM;
										inviteData = result;

										l(inviteData);
										if (inviteData.status == true) {
											$("#selectDuoPartnerModal").modal("hide");
											groupID = inviteData.GroupID;
											justCM = inviteData.justCM;
											l(data);
											var text = data.data.Text;
											playNotificationSound();
											bootbox.confirm(text, function(result) {
												setUserNot = setUserNotificationAsChecked(userNotID);
												setUserNot.success(function(setUserResult) {
													if (setUserResult.status == true) {
														if (result) {
															justLeaveQueue();
															if (document.URL.indexOf("/find_match.php") >= 0) {
																joinSingleQueueAsGroup2(groupID, false, justCM);
															}
															else {
																window.location = "find_match.php?rejoin=true&joinType=duoQueueJoin&gid=" + groupID
															}

															setTimeout(function() {
																notificationUpdate();
															}, 1000);
														}
														else {
															justLeaveQueue(groupID);
															setTimeout(function() {
																notificationUpdate();
															}, 1000);
														}
													}
												});
											});
										}
										else {
											setTimeout(function() {
												notificationUpdate();
											}, 1000);
											l("getDuoQueueInviteInfo fehler");
										}

									}
								});
							}
							else {
								inviteData.status = false;
								setTimeout(function() {
									notificationUpdate();
								}, 1000);
							}
						}
					});
					l("End getDuoQueueInviteInfo");

					break;
				case "2":
					l("Start Ping Player Notification");
					playPingNotificationSound();
					setUserNot = setUserNotificationAsChecked(userNotID);
					setUserNot.success(function(setUserResult) {
						
						setTimeout(function() {
							notificationUpdate();
						}, 1000);
					});
					
					l("End Ping Player Notification");
					break;
				case "3":
					l("Start General Notification");
					playPingNotificationSound();
					var text = data.data.Text;
					bootbox.confirm(text, function(result) {
						setUserNot = setUserNotificationAsChecked(userNotID);
						setUserNot.success(function(setUserResult) {
							setUserNot = setUserNotificationAsChecked(userNotID);
							setUserNot.success(function(setUserResult) {
								if (setUserResult.status == true) {
									if (result) {
										window.location = data.data.Href;
										setTimeout(function() {
											notificationUpdate();
										}, 1000);
									}
									else {
										setTimeout(function() {
											notificationUpdate();
										}, 1000);
									}
								}
							});
						});
					});
					
					
					l("End General Notification");
					break;
			}
			break;
		case "global":
			break;
	}
}

/*
 * Copyright 2013 Artur Leinweber Date: 2013-09-04
 */
function handleNotifications2(data) {
	l(data);
	var type = data.type;
	var userNotID = data.data.UserNotificationID;
	l("Type:" + type);
	switch (type) {
		case "user":
			l("hier!!!");
			var notTypeID = data.data.NotificationTypeID;
			l(notTypeID);
			switch (notTypeID) {
				case "1":
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
							if (result.status == true) {
								var inviteData = null;
								l("Start getJustCM");
								$.ajax({
									url : 'ajax.php',
									type : "POST",
									dataType : 'json',
									data : {
										type : "constant",
										mode : "justCM"
									},
									success : function(justCM) {
										l("getJustCM success");
										l(data);
										justCM = justCM.data;
										l("End getJustCM");
										result.justCM = justCM;
										inviteData = result;

										l(inviteData);
										if (inviteData.status == true) {
											$("#selectDuoPartnerModal").modal("hide");
											groupID = inviteData.GroupID;
											justCM = inviteData.justCM;
											l(data);
											var text = data.data.Text;
											playNotificationSound();
											bootbox.confirm(text, function(result) {
												setUserNot = setUserNotificationAsChecked(userNotID);
												setUserNot.success(function(setUserResult) {
													if (setUserResult.status == true) {
														if (result) {
															justLeaveQueue();
															if (document.URL.indexOf("/find_match.php") >= 0) {
																joinSingleQueueAsGroup2(groupID, false, justCM);
															}
															else {
																window.location = "find_match.php?rejoin=true&joinType=duoQueueJoin&gid=" + groupID
															}

															setTimeout(function() {
																getNotifcations();
															}, 4000);
														}
														else {
															justLeaveQueue(groupID);
															setTimeout(function() {
																getNotifcations();
															}, 5000);
														}
													}
												});
											});
										}
										else {
											setTimeout(function() {
												getNotifcations();
											}, 5000);
											l("getDuoQueueInviteInfo fehler");
										}

									}
								});
							}
							else {
								inviteData.status = false;
								setTimeout(function() {
									getNotifcations();
								}, 5000);
							}
						}
					});
					l("End getDuoQueueInviteInfo");

					break;
			}
			break;
		case "global":
			break;
	}
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function getNotifcations() {
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "Notification",
			mode : "getAllNotificationsForUser"
		},
		success : function(result) {
			if(result.status == true){
				handleNotifications2(result);
			}else{
				l("status false");
				l(result);
				setTimeout(function() {
					getNotifcations();
				}, 5000);
			}
		}
	});
}