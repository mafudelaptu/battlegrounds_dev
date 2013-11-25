/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
var timer = null;
function joinDuoSingleQueue(quickjoin, justCM) {

	l("joinDuoSingleQueue Start");
	// Modal einblenden
	$('#selectDuoPartnerModal').modal({
		backdrop : "static",
		keyboard : false
	});
	$("#searchForPlayerInput").keyup(function() {
		// Result area cleanen
		idResult = "searchForPlayerResults";
		$("#" + idResult).html("");

		query = $(this).val();

		// clearTimeout(timer);
		timer = setTimeout(function() {

			$.ajax({
				url : 'ajax.php',
				type : "POST",
				dataType : 'json',
				data : {
					type : "user",
					mode : "getAllUser",
					alsoSelf : false,
					query : query,
					typeahead : false
				},
				success : function(result) {
					l(result);
					// Ergebnisse anzeigen
					showResultsAfterKeyUp(idResult, result.data);

				}
			});
		}, 400);

	});

	// Button onclicks definieren
	$("#selectPartnerSubmitButton").click(function() {
		alert("click");
		l("click on Button!");
		// Error feld leeren
		$("#createGroupError").html("");

		// auslesen ob quickjoin
		quickJoin = $(this).attr("data-value");

		// ausgew‰hlten User auslesen
		partnerSteamID = $("input[name='createGroupSelection']:checked").val();
		partnerSteamIDAlreadyCreated = $("input[name='radioAlreadyCreatedGroups']:checked").val();
		l(partnerSteamID);
		// Already Created Groups JOIN
		if (typeof partnerSteamIDAlreadyCreated != 'undefined') {
			var groupID = partnerSteamIDAlreadyCreated;

			// Modal schlieﬂen und Queue Joinen
			$('#selectDuoPartnerModal').modal("hide");

			// Join Queue

			joinSingleQueueAsGroup(groupID, quickJoin, justCM);
		}
		// NEW GROUP JOIN
		else {
			if (typeof partnerSteamID != 'undefined') {
				// Gruppe erzeugen
				l("createDuoGroup Start");
				$.ajax({
					url : 'ajax.php',
					type : "POST",
					dataType : 'json',
					data : {
						type : "group",
						mode : "createDuoGroup",
						partnerID : partnerSteamID
					},
					success : function(result3) {
						l("createDuoGroup success");
						l(result3);
						var groupID = result3.data;
						// Modal schlieﬂen und Queue Joinen
						$('#selectDuoPartnerModal').modal("hide");

						// Join Queue

						joinSingleQueueAsGroup(groupID, quickJoin, justCM);

					}
				});
				l("createDuoGroup End");
			}
			else {
				$("#createGroupError").html("<div class='alert alert-error'>You have to select a partner to create a group!</div>");
			}
			l("End click on Button!");
		}

	});

	l("joinDuoSingleQueue End");

}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function joinDuoSingleQueue2(quickjoin, justCM) {

	l("joinDuoSingleQueue Start");
	// Modal einblenden
	$('#selectDuoPartnerModal').modal({
		backdrop : "static",
		keyboard : false
	});
	$("#searchForPlayerInput").keyup(function() {
		// Result area cleanen
		idResult = "searchForPlayerResults";
		$("#" + idResult).html("");

		query = $(this).val();

		clearTimeout(timer);
		timer = setTimeout(function() {

			$.ajax({
				url : 'ajax.php',
				type : "POST",
				dataType : 'json',
				data : {
					type : "user",
					mode : "getAllUser",
					alsoSelf : false,
					query : query,
					typeahead : false
				},
				success : function(result) {
					l(result);
					// Ergebnisse anzeigen
					showResultsAfterKeyUp(idResult, result.data);

				}
			});
		}, 400);

	});

	// Button onclicks definieren
	$("#selectPartnerSubmitButton")
			.click(function() {
				l("click on Button!");
				// Error feld leeren
				$("#createGroupError").html("");

				// auslesen ob quickjoin
				quickJoin = $(this).attr("data-value");

				// ausgew‰hlten User auslesen
				partnerSteamID = $("input[name='createGroupSelection']:checked").val();
				partnerSteamIDAlreadyCreated = $("input[name='radioAlreadyCreatedGroups']:checked").val();
				l(partnerSteamID);
				// Already Created Groups JOIN
				if (typeof partnerSteamIDAlreadyCreated != 'undefined') {
					var groupID = partnerSteamIDAlreadyCreated;

					// Modal schlieﬂen und Queue Joinen
					$('#selectDuoPartnerModal').modal("hide");

					l("Start checkAbleToJoinDuo");
					$
							.ajax({
								url : 'ajax.php',
								type : "POST",
								dataType : 'json',
								data : {
									type : "QueueGroup",
									mode : "checkAbleToJoinDuo",
									groupID : groupID
								},
								success : function(result) {
									l("checkAbleToJoinDuo success");
									l(result);
									if (result.status == true) {
										// send Notification to Duo-Partner
										sendNotificationToDuoPartner(groupID);

										// Join Queue
										joinSingleQueueAsGroup2(groupID, quickJoin, justCM);
									}
									else if (result.status == "inQueue") {
										bootbox
												.alert("<h3>Partner in Queue</h3>Your selected Duo-Partner is already in Queue with an other Duo-Team. You can't join the Duo-Queue with him right now.", function(result) {
													window.location = "find_match.php";
												});
									}
									else if (result.status == "inMatch") {
										bootbox
												.alert("<h3>Partner in Match</h3>Your selected Duo-Partner is in a Match right now.  You can't join the Duo-Queue with him at this moment.", function(result) {
													window.location = "find_match.php";
												});
									}
									else {
										l("smth went wrong (" + result.status + ")");
									}
								}
							});
					l("End checkAbleToJoinDuo");

				}
				// NEW GROUP JOIN
				else {
					if (typeof partnerSteamID != 'undefined') {
						// Gruppe erzeugen
						l("createDuoGroup Start");
						$.ajax({
							url : 'ajax.php',
							type : "POST",
							dataType : 'json',
							data : {
								type : "group",
								mode : "createDuoGroup",
								partnerID : partnerSteamID
							},
							success : function(result3) {
								l("createDuoGroup success");
								l(result3);
								var groupID = result3.data;
								// Modal schlieﬂen und Queue Joinen
								$('#selectDuoPartnerModal').modal("hide");

								// Join Queue

								joinSingleQueueAsGroup2(groupID, quickJoin, justCM);

							}
						});
						l("createDuoGroup End");
					}
					else {
						$("#createGroupError").html("<div class='alert alert-error'>You have to select a partner to create a group!</div>");
					}
					l("End click on Button!");
				}

			});

	l("joinDuoSingleQueue End");

}

/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function joinSingleQueueAsGroup(groupID, quickJoin, justCM) {
	alert(1);
	// Gruppe in Queue eintragen
	l("joinSingleQueueAsGroup Start");
	// Selektierte Spielmodi auslesen
	var spielmodi = null;
	var regions = null;
	if (quickJoin == "true") {
		if (justCM) {
			m = new Array();
			m.push("3");
			spielmodi = m;
		}
		else {
			spielmodi = getQuickJoinSingleQueueModi();
		}
		regions = getRegion();
	}
	else {
		alert(2);
		spielmodi = getSelectedSingleQueueModi();
		regions = getRegion();
	}
	l("spielmodi:");
	l(spielmodi);
	l("region:");
	l(regions);
	alert(regions);
	if (spielmodi.length > 0 && regions.length > 0) {
		$.ajax({
			url : 'ajax.php',
			type : "POST",
			dataType : 'json',
			data : {
				type : "QueueGroup",
				mode : "joinQueueAsGroup",
				ID : groupID,
				MM : spielmodi,
				regions : regions
			},
			success : function(result) {
				l("joinSingleQueueAsGroup success");

				// Leave Queue Button onlick
				$("#singleQueueLeaveQueueButton").click(function() {
					leaveQueue2(groupID);
				});

				l("checkIfPlayerIsBanned End");
				// Uhr starten
				$('#matchMakingClock').stopwatch('start');
				l("Uhr gestertet");
				// matchmakingInfo generieren
				generateSingleQueueMatchMakingInfo(quickJoin);

				// matchDetails leeren
				$("#matchmakingDetails").html("");

				// Modal √∂ffnen und MAtchmaking starten
				$('#myModalMatchMaking').modal({
					backdrop : "static",
					keyboard : false
				}).css({
					width : '81%',
					'margin-left' : function() {
						return -($(this).width() / 2);
					}
				});

				// matchmaking ansto√üen
				doSingleQueueMatchMaking(spielmodi, regions, quickJoin, groupID);
			}
		});
	}
	// Fehlerbehandlung
	else {
		if (spielmodi.length == 0) {
			error = '<p class="text-error">Select at least one Matchmode!</p>';
			$("#SingleQueueMatchModesErrors").append(error);
		}
		if (regions.length == 0) {
			error = '<p class="text-error">Select at least one Region!</p>';
			$("#SingleQueueRegionErrors").append(error);
		}

	}

	l("joinSingleQueueAsGroup End");
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function joinSingleQueueAsGroup2(groupID, quickJoin, justCM) {
	// Gruppe in Queue eintragen
	l("joinSingleQueueAsGroup2 Start");

	// Chat initialisieren
	initChat("findMatch", $.datepicker.formatDate('yy/mm/dd', new Date()), "findMatchChat"); /*
																								 * Start
																								 * the
																								 * inital
																								 * request
																								 */

	// Selektierte Spielmodi auslesen
	var spielmodi = null;
	var regions = null;
	if (quickJoin == "true") {
		if (justCM) {
			m = new Array();
			m.push("3");
			spielmodi = m;
		}
		else {
			spielmodi = getQuickJoinSingleQueueModi();
		}
		regions = getRegion();
	}
	else {
		spielmodi = getSelectedSingleQueueModi();
		regions = getRegion();
	}

	if (spielmodi.length > 0 && regions.length > 0) {
		$.ajax({
			url : 'ajax.php',
			type : "POST",
			dataType : 'json',
			data : {
				type : "QueueGroup",
				mode : "joinQueueAsGroup",
				ID : groupID,
				MM : spielmodi,
				regions : regions
			},
			success : function(result) {
				l("joinSingleQueueAsGroup2 success");
				l(result);
				if (result.status == true) {

					// Leave Queue Button onlick
					$("#singleQueueLeaveQueueButton").click(function() {
						leaveQueue2(groupID);
					});

					l("checkIfPlayerIsBanned End");
					// Uhr starten
					$('#matchMakingClock').stopwatch('start');
					l("Uhr gestertet");
					// matchmakingInfo generieren
					generateSingleQueueMatchMakingInfo(quickJoin);

					// matchDetails leeren
					$("#matchmakingDetails").html("");

					// hide position in queue
					$(".positionInQueue").hide();

					// Modal √∂ffnen und MAtchmaking starten
					$('#myModalMatchMaking').modal({
						backdrop : "static",
						keyboard : false
					}).css({
						width : '81%',
						'margin-left' : function() {
							return -($(this).width() / 2);
						}
					});

					// matchmaking ansto√üen
					doSingleQueueMatchMaking3(spielmodi, regions, quickJoin, groupID);
				}
				else if (result.status == "inQueue") {
					bootbox.alert("<h3>Partner in Queue</h3>Your selected Duo-Partner is already in Queue with an other Duo-Team. You can't join the Duo-Queue with him right now.", function(result) {
						window.location = "find_match.php";
					});
				}
				else if (result.status == "inMatch") {
					bootbox.alert("<h3>Partner in Match</h3>Your selected Duo-Partner is in a Match right now.  You can't join the Duo-Queue with him at this moment.", function(result) {
						window.location = "find_match.php";
					});
				}
				else {
					l("smth went wrong (" + result.status + ")");
				}
			}
		});
	}
	// Fehlerbehandlung
	else {
		if (spielmodi.length == 0) {
			error = '<p class="text-error">Select at least one Matchmode!</p>';
			$("#SingleQueueMatchModesErrors").append(error);
		}
		if (regions.length == 0) {
			error = '<p class="text-error">Select at least one Region!</p>';
			$("#SingleQueueRegionErrors").append(error);
		}

	}

	l("joinSingleQueueAsGroup2 End");
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function deselectRadioButtons(area) {
	switch (area) {
		case "create":
			$("input[name='createGroupSelection']").attr("checked", false);
			break;
		case "existing":
			$("input[name='radioAlreadyCreatedGroups']").attr("checked", false);
			break;
	}
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function checkIfAlreadyInQueueWithGroup() {
	l("checkIfAlreadyInQueueWithGroup Start");
	ret = $.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "QueueGroup",
			mode : "checkIfAlreadyInQueueWithGroup"
		}
	});
	return ret;
	l("checkIfAlreadyInQueueWithGroup End");
}

function sendNotificationToDuoPartner(groupID) {
	l("Start sendNotificationToDuoPartner");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "userNotification",
			mode : "sendNotificationToDuoPartner",
			groupID : groupID
		},
		success : function(result) {
			l("sendNotificationToDuoPartner success");
			l(result);
			if (result.status !== true) {

			}
			else {

			}
		}
	});
	l("End sendNotificationToDuoPartner");
}