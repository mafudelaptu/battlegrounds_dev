/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */

var waitingTime4AcceptReady = 15000;
var timeout;
var waitingTime4AllReady = 20000;
var intervalID = null;
var cancelTimeoutID = null;
var runnedTime = 0;
var timePulling = 5000;
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function joinSingleQueue3(quickJoin, groupID, justCM) {
	l("joinSingleQueue3 Start");

	// Carousel stoppen
	$("#SingleQueueCarousel").carousel('pause');

	// Chat initialisieren
	initChat("findMatch", $.datepicker.formatDate('yy/mm/dd', new Date()), "findMatchChat"); /*
																								 * Start
																								 * the
																								 * inital
																								 * request
																								 */

	/*
	 * Queue leeren
	 * 
	 */
	// 
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "queue",
			mode : "leaveQueue2"
		},
		success : function(result) {
			l("leaveQueue2 success");
		}
	});

	// QueueLock pr�fen
	l("checkIfPlayerIsBanned Start");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "QueueLock",
			mode : "checkLock"
		},
		success : function(queueLockResult) {
			l("checkLock success");
			l(queueLockResult);
			if (queueLockResult.status == true) {
				// Checken ob Spieler gebannt ist
				l("checkIfPlayerIsBanned Start");
				$.ajax({
					url : 'ajax.php',
					type : "POST",
					dataType : 'json',
					data : {
						type : "banlist",
						mode : "checkForBansOfPlayer"
					},
					success : function(result) {
						l("checkIfPlayerIsBanned success");
						l(result);
						l(result.banned);
						var banCounts = result.banCounts;
						var display = result.display;
						if (result.banned == true || (banCounts == 1 && display == 1)) {

							l("gebannten Spieler behandeln");
							// gebannten Spieler behandeln
							$.ajax({
								url : 'ajax.php',
								type : "POST",
								dataType : 'json',
								data : {
									type : "banlist",
									mode : "getCurrentBanDataOfPlayer"
								},
								success : function(result2) {
									l("getCurrentBanDataOfPlayer success");
									l(result2);
									l("BANS:" + banCounts);
									if (result2.status) {
										bannedTillTimestamp = result2.data.BannedTill;
										bannedAtTimestamp = result2.data.BannedAt;
										bannedAt = new Date(bannedAtTimestamp * 1000).format('d.m.Y - h:i:s');
										bannedTill = new Date(bannedTillTimestamp * 1000).format('d.m.Y - h:i:s');
										l(bannedTill);

										bannedBy = result2.data.Reason + " - <img src='" + result2.data.Avatar + "'><a href='profile.php?ID=" + result2.data.BannedBySteamID + "'>" + result2.data.Name
												+ "</a>";

										banReasonText = result2.data.BanReasonText;
										text = "<div align='center'><h4>You got banned " + bannedBy + "</h4> " + "<p>at " + bannedAt + " till " + bannedTill + "</p>" + "<p class='well'>"
												+ banReasonText + "</p>" + "<h3>It is your " + banCounts + ". Ban!</h3>"
												+ "<p>Until then you cant join a Queue! Try be more mannered next time.</p></div>";

										bootbox.alert(text, function() {

										});
									}
									else {
										if (banCounts == 1) {
											text = "<div align='center'><h4>You got the first time banned!</h4>" + "<p>Now it is just a warning!</p>"
													+ "<p>Next time you get a 24 hours ban. Please be more mannered in future.</p></div>";

											bootbox.alert(text, function() {
												cleanBansOfPlayer();
											});
										}
										else {
											text = "<div align='center'><h4>you are not banned anymore!</h4>" + "<p>Now you can join a Queue again!</p></div>";

											bootbox.alert(text, function() {
												cleanBansOfPlayer();
											});

											l(result2);
										}
									}
								}
							});
						}
						else {

							// kontrollieren ob nicht schon mit
							// Duo Queue drin
							var duoQueue = checkIfAlreadyInQueueWithGroup();
							duoQueue.success(function(result) {
								if (result.inQueue) {
									text = "<div align='center'><h4>You are already in DuoQueue with Group:#" + result.GroupID + "!</h4>"
											+ "<p>Please use 'Duo-Queue-Join' to join the Queue!</p></div>";

									bootbox.alert(text, function() {
										window.location = "find_match.php";
									});
								}
								else {
									l("#################### TEST!");
									l(ret);
									// ganz normal Queue
									// joinen
									// Selektierte
									// Spielmodi
									// auslesen
									var spielmodi = null;
									var regions = null;
									if (quickJoin == true) {
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

									$("#singleQueueArea p[class='text-error']").remove(); // Error
									// wieder
									// löschen

									if (spielmodi.length > 0 && regions.length > 0) {

										// beim
										// Verlassen der
										// Seite eine
										// Warnung
										// anzeigen:
										// aktivieren
										setConfirmUnload(true);

										$.each(spielmodi, function(index, value) {
											$.cookie("singleQueue[" + value + "]", true, {
												expires : 14
											}); // cookies
											// für
											// alle
											// ausgewählten
											// Spielmodi
											// setzen
										});

										$.ajax({
											url : 'ajax.php',
											type : "POST",
											dataType : 'json',
											data : {
												type : "queue",
												mode : "joinSingleQueue2",
												modi : spielmodi,
												region : regions
											},
											success : function(result) {
												l("joinSingleQueue success");
												l(result);

												if (result.status != "inMatch") {
													// MatchTeams
													// info
													// zur
													// sicherheit
													// l�schen
													// cleanMatchTeamsOfPlayer();

													setTimeout(function() {
														// Uhr
														// starten
														$('#matchMakingClock').stopwatch('start');
														l("Uhr gestertet");
														// matchmakingInfo
														// generieren
														generateSingleQueueMatchMakingInfo(quickJoin, justCM);

														// matchDetails
														// leeren
														$("#matchmakingDetails").html("");

														// Modal
														// öffnen
														// und
														// MAtchmaking
														// starten
														$('#myModalMatchMaking').modal({
															backdrop : "static",
															keyboard : false
														}).css({
															width : '81%',
															'margin-left' : function() {
																return -($(this).width() / 2);
															
															}
														});

														// matchmaking
														// anstoßen
														doSingleQueueMatchMaking2(spielmodi, regions, quickJoin, groupID);
													}, 400);

												}
												else {
													l("already in Match!");
													bootbox.alert("You are already in a Match! Please check your notifications! (top right)", function() {
														// beim
														// Verlassen
														// der
														// Seite
														// eine
														// Warnung
														// anzeigen:
														// aktivieren
														setConfirmUnload(false);

														setTimeout(function() {
															window.location = "find_match.php";
														}, 100);
													});
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
								}
							});

						}
					}
				});
			}
			// Queue Lock msg
			else {
				text = "<div align='center'><h4>You just declined a match or you were afk!</h4>" + "<p>You can't queue for <strong>" + queueLockResult.time
						+ "</strong>. Please be ready next time.</p></div>";

				bootbox.alert(text, function() {
					window.location = "find_match.php";
				});
			}
		}
	});

	// Leave Queue Button onlick
	$("#singleQueueLeaveQueueButton").click(function() {
		leaveQueue2(groupID);
	});
	l("checkIfPlayerIsBanned End");

}

/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function joinSingleQueue(quickJoin, groupID, justCM) {
	l("Start joinSingleQueue ");

	// Chat initialisieren
	initChat("findMatch", $.datepicker.formatDate('yy/mm/dd', new Date()), "findMatchChat");

	// set join Mode
	var joinMode = "singleQueue";
	
	// check requirements and stuff for joining Queue
	// also leave Queue before joining
	checkData = checkJoinQueue();
	checkData.success(function(checkResult) {
		l(checkResult);
		switch (checkResult.status) {
			case true:
				joinSingleQueueFkt(quickJoin, groupID, justCM, joinMode);
				break;

			case "inMatch":
				l("already in Match!");
				bootbox.alert("You are already in a Match! Please check your notifications! (top right)", function() {
					// beim Verlassen der Seite eine Warnung anzeigen:
					// deaktivieren
					setConfirmUnload(false);
					window.location = "openMatches.php";

				});
				break;
			case "queueLock":
				var time = checkResult.time;
				text = "<div align='center'><h4>You just declined a match or you were afk!</h4>" + "<p>You can't queue for <strong>" + time + "</strong>. Please be ready next time.</p></div>";

				bootbox.alert(text, function() {
					window.location = "find_match.php";
				});
				break;
			case "banned":
				var rejoin = getParameterByName("rejoin");
				if(rejoin != "true"){
					var banCounts = checkResult.banCounts;
					if (checkResult.banned) {
						var bannedTillTimestamp = checkResult.data.BannedTill;
						var bannedAtTimestamp = checkResult.data.BannedAt;
						var bannedAt = new Date(bannedAtTimestamp * 1000).format('d.m.Y - H:i:s');
						var bannedTill = new Date(bannedTillTimestamp * 1000).format('d.m.Y - H:i:s');
						l(bannedTill);

						var bannedBy = checkResult.bannData.Reason + " - <img src='" + checkResult.bannData.Avatar + "'><a href='profile.php?ID=" + checkResult.bannData.BannedBySteamID + "'>"
								+ checkResult.bannData.Name + "</a>";

						var banReasonText = checkResult.data.BanReasonText;
						var text = "<div align='center'><h4>You got warned " + bannedBy + "</h4> " + "<p>at " + bannedAt + " till " + bannedTill + "</p>" + "<p class='well'>" + banReasonText + "</p>"
								+ "<h3>It is your " + banCounts + ". warn!</h3>" + "<p>Until then you cant join normal Queue and stay in Prison-Queue! Try be more mannered next time.</p></div>";

						bootbox.alert(text, function(){
							joinSingleQueueFkt(quickJoin, groupID, justCM);
						});
					}
					else {
						if (banCounts == 1) {
							var text = "<div align='center'><h4>You got one active warn!</h4>"
									+ "<p>For the next warn you get a 24 hours Prison-Queue-Time. Please be more mannered in future.</p></div>";

							bootbox.alert(text, function() {
								//cleanBansOfPlayer();
								joinSingleQueueFkt(quickJoin, groupID, justCM);
							});
						}
						else {
							var text = "<div align='center'><h4>you are not banned anymore!</h4>" + "<p>Now you can join the Normal-Queue again!</p></div>";

							bootbox.alert(text, function() {
								joinSingleQueueFkt(quickJoin, groupID, justCM);
							});
						}
					}
				}
				
				break;
			case "inDuoQueue":
				text = "<div align='center'><h4>You are already in DuoQueue with Group:#" + checkResult.GroupID + "!</h4>" + "<p>Please use 'Duo-Queue-Join' to join the Queue!</p></div>";

				bootbox.alert(text, function() {
					window.location = "find_match.php?rejoin=true&joinType=duoQueueJoin&gid=" + checkResult.GroupID;
				});
				break;
			case "permaBanned":
				text = "<div align='center'><h4>You got permanently banned from system</h4><p>Check your warns in your profile to get more information.</p></div>";

				bootbox.alert(text, function() {
					window.location = "profile.php#warns";
				});
				break;
		}
	});

	// Leave Queue Button onlick
	$("#singleQueueLeaveQueueButton").click(function() {
		leaveQueue2(groupID);
	});

	l("End joinSingleQueue");
}

/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function joinSingleQueue2(quickJoin, groupID) {
	l("joinSingleQueue2 Start");

	// Carousel stoppen
	$("#SingleQueueCarousel").carousel('pause');

	// Checken ob Spieler gebannt ist
	l("checkIfPlayerIsBanned Start");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "banlist",
			mode : "checkForBansOfPlayer"
		},
		success : function(result) {
			l("checkIfPlayerIsBanned success");
			l(result);
			l(result.banned);
			var banCounts = result.banCounts;
			var display = result.display;
			if (result.banned == true || (banCounts == 1 && display == 1)) {

				l("gebannten Spieler behandeln");
				// gebannten Spieler behandeln
				$.ajax({
					url : 'ajax.php',
					type : "POST",
					dataType : 'json',
					data : {
						type : "banlist",
						mode : "getCurrentBanDataOfPlayer"
					},
					success : function(result2) {
						l("getCurrentBanDataOfPlayer success");
						l(result2);
						l("BANS:" + banCounts);
						if (result2.status) {
							bannedTillTimestamp = result2.data.BannedTill;
							bannedAtTimestamp = result2.data.BannedAt;
							bannedAt = new Date(bannedAtTimestamp * 1000).format('d.m.Y - h:i:s');
							bannedTill = new Date(bannedTillTimestamp * 1000).format('d.m.Y - h:i:s');
							l(bannedTill);

							bannedBy = result2.data.Reason;

							text = "<div align='center'><h4>You got banned " + bannedBy + "</h4> " + "<p>at " + bannedAt + " till " + bannedTill + "</p>" + "<h3>It is your " + banCounts
									+ ". Ban!</h3>" + "<p>Until then you cant join a Queue! Try be more mannered next time.</p></div>";

							bootbox.alert(text, function() {

							});
						}
						else {
							if (banCounts == 1) {
								text = "<div align='center'><h4>You got the first time banned!</h4>" + "<p>Now it is just a warning!</p>"
										+ "<p>Next time you get a 24 hours ban. Please be more mannered in future.</p></div>";

								bootbox.alert(text, function() {
									cleanBansOfPlayer();
								});
							}
							else {
								text = "<div align='center'><h4>you are not banned anymore!</h4>" + "<p>Now you can join a Queue again!</p></div>";

								bootbox.alert(text, function() {
									cleanBansOfPlayer();
								});

								l(result2);
							}
						}
					}
				});
			}
			else {

				// kontrollieren ob nicht schon mit Duo Queue drin
				var duoQueue = checkIfAlreadyInQueueWithGroup();
				duoQueue.success(function(result) {
					if (result.inQueue) {
						text = "<div align='center'><h4>You are already in DuoQueue with Group:#" + result.GroupID + "!</h4>" + "<p>Please use 'Duo-Queue-Join' to join the Queue!</p></div>";

						bootbox.alert(text, function() {
							window.location = "find_match.php";
						});
					}
					else {
						l("#################### TEST!");
						l(ret);
						// ganz normal Queue joinen
						// Selektierte Spielmodi auslesen
						var spielmodi = null;
						var regions = null;
						if (quickJoin == true) {
							spielmodi = getQuickJoinSingleQueueModi();
							regions = getQuickJoinSingleQueueRegions();
						}
						else {
							spielmodi = getSelectedSingleQueueModi();
							regions = getSelectedSingleQueueRegions();
						}

						$("#singleQueueArea div[class='media-body'] p[class='text-error']").remove(); // Error
						// wieder
						// löschen

						if (spielmodi.length > 0 && regions.length > 0) {

							// beim Verlassen der Seite eine
							// Warnung anzeigen: aktivieren
							setConfirmUnload(true);

							$.each(spielmodi, function(index, value) {
								$.cookie("singleQueue[" + value + "]", true, {
									expires : 14
								}); // cookies für alle
								// ausgewählten Spielmodi
								// setzen
							});

							$.ajax({
								url : 'ajax.php',
								type : "POST",
								dataType : 'json',
								data : {
									type : "queue",
									mode : "joinSingleQueue2",
									modi : spielmodi,
									region : regions
								},
								success : function(result) {
									l("joinSingleQueue success");
									l(result);

									if (result.status != "inMatch") {
										// MatchTeams
										// info zur
										// sicherheit
										// l�schen
										// cleanMatchTeamsOfPlayer();

										setTimeout(function() {
											// Uhr
											// starten
											$('#matchMakingClock').stopwatch('start');
											l("Uhr gestertet");
											// matchmakingInfo
											// generieren
											generateSingleQueueMatchMakingInfo(quickJoin);

											// matchDetails
											// leeren
											$("#matchmakingDetails").html("");

											// Modal
											// öffnen
											// und
											// MAtchmaking
											// starten
											$('#myModalMatchMaking').modal({
												backdrop : "static",
												keyboard : false
											});

											// matchmaking
											// anstoßen
											doSingleQueueMatchMaking(spielmodi, regions, quickJoin, groupID);
										}, 400);

									}
									else {
										l("already in Match!");
										bootbox.alert("You are already in a Match! Please check your notifications! (top right)", function() {
											// beim
											// Verlassen
											// der
											// Seite
											// eine
											// Warnung
											// anzeigen:
											// aktivieren
											setConfirmUnload(false);

											setTimeout(function() {
												window.location = "find_match.php";
											}, 100);
										});
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
					}
				});

			}
		}
	});
	// Leave Queue Button onlick
	$("#singleQueueLeaveQueueButton").click(function() {
		leaveQueue2(groupID);
	});
	l("checkIfPlayerIsBanned End");

}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function getSelectedSingleQueueModi() {
	ret = new Array();
	// buttons = $("#singleQueueArea div[data-toggle='buttons-checkbox'] >
	// button");
	badges = $("#SingleQueueMatchModes .badge");
	l(badges);
	$.each(badges, function(index, value) {
		var val = $(value).attr("data-value");
		// $.cookie("singleQueue[" + $(value).val() + "]", null); // alle
		// Spielmodi
		// Cookies
		// löschen
		// if ($(value).hasClass("active")) {
		// ret.push($(value).val());
		// }
		ret.push(val);
	});
	l(ret);
	return ret;
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function getSelectedSingleQueueRegions() {
	ret = new Array();
	badges = $("#SingleQueueRegion .badge");
	l(badges);
	$.each(badges, function(index, value) {
		var val = $(value).attr("data-value");
		// $.cookie("singleQueue[" + $(value).val() + "]", null); // alle
		// Spielmodi
		// Cookies
		// löschen
		// if ($(value).hasClass("active")) {
		// ret.push($(value).val());
		// }
		ret.push(val);
	});
	l(ret);
	return ret;
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function getQuickJoinSingleQueueModi() {
	l("getQuickJoinSingleQueueModi Start");
	var quali = $("#qualiHiddenIdentifier").val();
	var ret = new Array();
	if (typeof quali != "undefined") {
		ret.push("9"); // CD
	}
	else {
		ret.push("1"); // AP
		ret.push("2"); // SD
		ret.push("7"); // RD
		ret.push("8"); // AR
	}

	l(ret);
	l("getQuickJoinSingleQueueModi End");
	return ret;
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function getQuickJoinSingleQueueRegions() {
	ret = new Array();
	ret.push("1"); // AUTO
	ret.push("2"); // AUTO
	ret.push("3"); // AUTO
	ret.push("4"); // AUTO
	ret.push("5"); // AUTO
	ret.push("6"); // AUTO
	ret.push("7"); // AUTO
	ret.push("8"); // AUTO
	ret.push("9"); // AUTO
	l(ret);
	return ret;
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
doMatchmakingTimeout = null;
function doSingleQueueMatchMaking(spielmodi, regions, quickJoin, groupID) {
	// Prüfen ob spieler schon mit anderen gematchmaked wurde
	alreadyMatchMaked = false;

	// auslesen ob forceSearch checkbox activiert wurde
	forceChecked = $("#forceSearching").attr("checked");

	if (forceChecked == "checked") {
		force = true;
	}
	else {
		force = false;
	}
	l("########==================== GROUP TEST");
	l(groupID);

	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "queue",
			mode : "updateForceSearch",
			forceSearch : force
		},
		success : function(result) {
			l(result);
			// $("#matchmakingDetails").append(result.debug);
		}
	});

	if (!alreadyMatchMaked) {

		$.ajax({
			url : 'ajax.php',
			type : "POST",
			dataType : 'json',
			data : {
				type : "matchmaking",
				mode : "singleQueueSearch",
				modi : spielmodi,
				region : regions,
				forceSearch : force
			},
			success : function(result) {
				l(result);

				updatePlayersFound(result.queue);

				updateRange(result.range);

				if (result.status == "searching") {
					// $("#DEBUG_AREA").append(result.test);

					// matchMaking-Details aktualisieren
					// $("#matchmakingDetails")
					// .append(result.matchDetails);
					// // scroll to bottom
					// document.getElementById('wrapMatchMakingDetails').scrollTop
					// = 1000000000;

					var duoQueue = checkIfAlreadyInQueueWithGroup();
					var inMatchTeams = checkAlreadyInMatchTeams();
					duoQueue.success(function(result) {
						// inMatchTeams.success(function(result2){
						l("================TEEEST=========");
						// l(result2);
						l(result);
						if (!result.inQueue && groupID > 0) {
							inMatchTeams.success(function(result2) {
								l(result2);
							});
							// beim Verlassen der Seite eine
							// Warnung anzeigen: aktivieren
							setConfirmUnload(false);

							clearTimeout(doMatchmakingTimeout);
							doMatchmakingTimeout = null;

							// JoinQueue Modal schlie�en
							$("#myModalMatchMaking").modal("hide");

							text = "<div align='center'><h4>Your Partner of Group:#" + groupID + " left the Queue!</h4>" + "<p>You just got also kicked from Queue. </p></div>";

							bootbox.alert(text, function() {
								window.location = "find_match.php";
							});
						}
						else {
							doMatchmakingTimeout = setTimeout(function() {
								doSingleQueueMatchMaking(spielmodi, regions, quickJoin, groupID);
							}, timePulling);
						}
						// });

					});
					// doMatchmakingTimeout = setTimeout(function() {
					// doSingleQueueMatchMaking(spielmodi, regions,
					// quickJoin, groupID);
					// }, timePulling);
				}
				else if (result.status == "finished" || result.status == "finished_through_other_player") {

					clearTimeout(doMatchmakingTimeout);
					doMatchmakingTimeout = null;

					l("################### SAVE!!!!!");
					// MatchDetails einfuegen
					saveMatchDetails(result.matchID);
					l("################### SAVE END!!!!!");

					// match found Sound abspielen
					playMatchFoundSound();
					// $.playSound("files/sound/matchReadySound.wav");

					setTimeout(function() {
						// aktuelles Modal schlie�en und
						// ReadyMatchModal
						// �ffnen
						$("#myModalMatchMaking").modal("hide");
						matchWasFoundModal2(result.matchID, quickJoin, groupID);
					}, 1000);
				}

			}
		});
	}
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function doSingleQueueMatchMaking2(spielmodi, regions, quickJoin, groupID) {
	// Prüfen ob spieler schon mit anderen gematchmaked wurde
	alreadyMatchMaked = false;

	// auslesen ob forceSearch checkbox activiert wurde
	forceChecked = $("#forceSearching").attr("checked");

	if (forceChecked == "checked") {
		force = true;
	}
	else {
		force = false;
	}
	l("########==================== GROUP TEST");
	l(groupID);

	if (!alreadyMatchMaked) {

		$.ajax({
			url : 'ajax.php',
			type : "POST",
			dataType : 'json',
			data : {
				type : "matchmaking",
				mode : "singleQueueSearch2",
				modi : spielmodi,
				region : regions,
				forceSearch : force
			},
			success : function(result) {
				l(result);

				updatePlayersFound(result.queue);
				updateRange(result.range);
				updateUserPool(result.skillBracket);
				updateNextMatchmakingTime(result.nextMatchmaking);
				updateQueueStats(result.queueCounts);

				if (result.status == "searching") {
					// $("#DEBUG_AREA").append(result.test);

					// matchMaking-Details aktualisieren
					// $("#matchmakingDetails")
					// .append(result.matchDetails);
					// // scroll to bottom
					// document.getElementById('wrapMatchMakingDetails').scrollTop
					// = 1000000000;

					var duoQueue = checkIfAlreadyInQueueWithGroup();
					var inMatchTeams = checkAlreadyInMatchTeams();

					duoQueue.success(function(result) {
						// inMatchTeams.success(function(result2){
						l("================TEEEST=========");
						// l(result2);
						l(result);
						if (!result.inQueue && groupID > 0) {
							inMatchTeams.success(function(result2) {
								l(result2);
								if (result2.status == false) {
									// beim Verlassen der Seite eine
									// Warnung anzeigen: aktivieren
									setConfirmUnload(false);

									clearTimeout(doMatchmakingTimeout);
									doMatchmakingTimeout = null;

									// JoinQueue Modal schlie�en
									$("#myModalMatchMaking").modal("hide");

									text = "<div align='center'><h4>Your Partner of Group:#" + groupID + " left the Queue!</h4>" + "<p>You just got also kicked from Queue. </p></div>";

									bootbox.alert(text, function() {
										window.location = "find_match.php";
									});
								}
								else {
									doMatchmakingTimeout = setTimeout(function() {
										doSingleQueueMatchMaking2(spielmodi, regions, quickJoin, groupID);
									}, timePulling);
								}
							});

						}
						else {
							doMatchmakingTimeout = setTimeout(function() {
								doSingleQueueMatchMaking2(spielmodi, regions, quickJoin, groupID);
							}, timePulling);
						}
						// });

					});

					// doMatchmakingTimeout = setTimeout(function() {
					// doSingleQueueMatchMaking(spielmodi, regions,
					// quickJoin, groupID);
					// }, timePulling);
				}
				else if (result.status == "finished") {

					clearTimeout(doMatchmakingTimeout);
					doMatchmakingTimeout = null;

					l("################### SAVE!!!!!");
					// MatchDetails einfuegen
					// saveMatchDetails(result.matchID);
					l("################### SAVE END!!!!!");

					// match found Sound abspielen
					playMatchFoundSound();
					// $.playSound("files/sound/matchReadySound.mp3");

					// title blinken lassen wenn match geufnden
					$.titleAlert("Match found!", {
						stopOnFocus : true,
						duration : 0,
						interval : 500
					});

					setTimeout(function() {
						// aktuelles Modal schlie�en und
						// ReadyMatchModal
						// �ffnen
						$("#myModalMatchMaking").modal("hide");
						matchWasFoundModal2(result.matchID, quickJoin, groupID);
					}, 1000);
				}

			}
		});
	}
}

/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function doSingleQueueMatchMaking3(spielmodi, regions, quickJoin, groupID, joinMode) {
	// Prüfen ob spieler schon mit anderen gematchmaked wurde
	alreadyMatchMaked = false;

	// auslesen ob forceSearch checkbox activiert wurde
	forceChecked = $("#forceSearching").attr("checked");

	if (forceChecked == "checked") {
		force = true;
	}
	else {
		force = false;
	}
	l("########==================== GROUP TEST");
	l(groupID);

	if (!alreadyMatchMaked) {

		$.ajax({
			url : 'ajax.php',
			type : "POST",
			dataType : 'json',
			data : {
				type : "matchmaking",
				mode : "singleQueueSearch2",
				modi : spielmodi,
				region : regions,
				forceSearch : force
			},
			success : function(result) {
				l(result);

				updatePlayersFound(result.queue);
				updateRange(result.range);
				updateUserPool(result.skillBracket);
				updateNextMatchmakingTime(result.nextMatchmaking);
				updateQueueStats(result.queueCounts);

				if (result.status == "searching") {
					// check still in DuoQueue?
					if (result.inQueue == false && groupID > 0) {
						if (result.inMatchTeams == false) {
							// beim Verlassen der Seite eine
							// Warnung anzeigen: aktivieren
							setConfirmUnload(false);

							clearTimeout(doMatchmakingTimeout);
							doMatchmakingTimeout = null;

							// JoinQueue Modal schlie�en
							$("#myModalMatchMaking").modal("hide");

							text = "<div align='center'><h4>Your Partner of Group:#" + groupID + " left the Queue!</h4>" + "<p>You just got also kicked from Queue. </p></div>";

							bootbox.alert(text, function() {
								window.location = "find_match.php";
							});
						}
						else {
							doMatchmakingTimeout = setTimeout(function() {
								doSingleQueueMatchMaking3(spielmodi, regions, quickJoin, groupID, joinMode);
							}, timePulling);
						}
					}
					else {
						doMatchmakingTimeout = setTimeout(function() {
							doSingleQueueMatchMaking3(spielmodi, regions, quickJoin, groupID, joinMode);
						}, timePulling);
					}
				}
				else if (result.status == "finished") {

					clearTimeout(doMatchmakingTimeout);
					doMatchmakingTimeout = null;

					l("################### SAVE!!!!!");
					// MatchDetails einfuegen
					// saveMatchDetails(result.matchID);
					l("################### SAVE END!!!!!");

					// match found Sound abspielen
					playMatchFoundSound();
					// $.playSound("files/sound/matchReadySound.mp3");

					// title blinken lassen wenn match geufnden
					$.titleAlert("Match found!", {
						stopOnFocus : true,
						duration : 0,
						interval : 500
					});

					setTimeout(function() {
						// aktuelles Modal schlie�en und
						// ReadyMatchModal
						// �ffnen
						$("#myModalMatchMaking").modal("hide");
						matchWasFoundModal2(result.matchID, quickJoin, groupID, joinMode);
					}, 1000);
				}
				else if (result.status == "notInQueue") {
					if(result.inQueue == false && groupID > 0){
						// beim Verlassen der Seite eine
						// Warnung anzeigen: aktivieren
						setConfirmUnload(false);

						clearTimeout(doMatchmakingTimeout);
						doMatchmakingTimeout = null;

						// JoinQueue Modal schlie�en
						$("#myModalMatchMaking").modal("hide");

						text = "<div align='center'><h4>Your Partner of Group:#" + groupID + " left the Queue!</h4>" + "<p>You just got also kicked from Queue. </p></div>";

						bootbox.alert(text, function() {
							window.location = "find_match.php";
						});
					}
					else{
						setConfirmUnload(false);
						clearTimeout(doMatchmakingTimeout);
						doMatchmakingTimeout = null;
						
						// JoinQueue Modal schlie�en
						$("#myModalMatchMaking").modal("hide");
						text = "<div align='center'><h4>You are not in Queue anymore</h4>" +
								"<p>You had an error in queueing! Please rejoin the Queue.</p>" +
								"</div>";

						bootbox.alert(text, function() {
							window.location = "find_match.php";
						});
					}
					
				}
			}
		});
	}
}

/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function leaveQueue2(groupID) {
	l("leaveQueue2 Start");

	// Uhr resetten
	$('#matchMakingClock').stopwatch('reset');
	$('#matchMakingClock').stopwatch('stop');
	$('#matchMakingClock').html("");

	// timeout Beenden
	clearTimeout(doMatchmakingTimeout);
	doMatchmakingTimeout = null;

	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "queue",
			mode : "leaveQueue2"
		},
		success : function(result) {
			l("leaveQueue2 success");
			// SeitenWarnung daktivieren
			setConfirmUnload(false);

			var cleanGroup = cleanQueueGroup2(groupID);
			cleanGroup.success(function(result2){
				l(result2);
				
				//setTimeout(function() {
					window.location = "find_match.php";
			//	}, 1000);
				l("leaveQueue2 End");
			});			
		}
	});

	
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
// für notification - duo queue
function justLeaveQueue(groupID) {
	l("justLeaveQueue Start");

	// Uhr resetten
	$('#matchMakingClock').stopwatch('reset');
	$('#matchMakingClock').stopwatch('stop');
	$('#matchMakingClock').html("");

	// timeout Beenden
	clearTimeout(doMatchmakingTimeout);
	doMatchmakingTimeout = null;

	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "queue",
			mode : "leaveQueue2"
		},
		success : function(result) {
			l("leaveQueue2 success");
			// SeitenWarnung daktivieren
			setConfirmUnload(false);

			cleanQueueGroup(groupID);

			l("justLeaveQueue End");
		}
	});

	
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function matchWasFoundModal2(matchID, quickJoin, groupID, joinMode) {
	l("matchWasFoundModal2 Start");
	$("#myModalReadyMatch span.countdown").stop(true);
	$("#myModalReadyMatch span.countdown").html("");

	clearTimeout(timeout);
	timeout = null;

	// initialisier Countdown
	waitingDauer = waitingTime4AcceptReady / 1000;

	$("#myModalReadyMatch span.countdown").countDown({
		startNumber : waitingDauer,
		startFontSize : "20px",
		endFontSize : "20px",
	});

	// Modal anzeigen
	$('#myModalReadyMatch').modal({
		backdrop : "static",
		keyboard : false
	});

	// Nach 15 Sekunden Player aus der Queue hauen
	timeout = setTimeout(function() {
		clearTimeout(timeout);
		timeout = null;
		kickFromQueue(matchID, "autoKick", quickJoin, true, groupID, joinMode);
	}, waitingTime4AcceptReady);

	// Wenn Accept dann nächsten Modal aufmachen
	$("#myModalReadyMatchAcceptButton").click(function() {
		l("matchWasFoundModal Accept clicked");
		$("#myModalReadyMatch span.countdown").html("");
		clearTimeout(timeout);
		timeout = null;
		acceptMatch2(matchID, quickJoin, groupID, joinMode);
	});

	// Wenn Cancel dann Player aus der Queue hauen
	$("#myModalReadyMatchCancelButton").click(function() {
		clearTimeout(timeout);
		timeout = null;
		l("matchWasFoundModal Cancel clicked");
		$("#myModalReadyMatch span.countdown").html("");
		kickFromQueue(matchID, "decline", quickJoin, true, groupID, joinMode);
	});

	l("matchWasFoundModal2 End");
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function acceptMatch2(matchID, quickJoin, groupID, joinMode) {
	l("acceptMatch2 Start");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "match",
			mode : "accept",
			ID : matchID,
			groupID : groupID
		},
		success : function(result) {
			l("acceptMatch2 success");
			if (result.status == true) {
				$('#myModalReadyMatch').modal('hide');
				l("acceptMatch hide modal");
				$('#myModalReadyMatch').on('hidden', function() {

					// Player aus der Queue hauen, da schon n schritt weiter
					// leaveQueue2();

					switch(joinMode){
						case "1vs1Queue":
							$("#spanWaitingMatchID1vs1").html(matchID);
							// show Waiting for all Ready Modal
							$('#waitingForAllReady1vs1').modal({
								backdrop : "static",
								keyboard : false
							});
							// Countdown anwerfen
							$("#waitingForAllReady1vs1 span.countdown").stop(true);
							$("#waitingForAllReady1vs1 span.countdown").html("");
							waitingDauer = waitingTime4AllReady / 1000;
							$("#waitingForAllReady1vs1 span.countdown").countDown({
								startNumber : waitingDauer,
								startFontSize : "20px",
								endFontSize : "20px",
							});
							break;
						default:
							$("#spanWaitingMatchID").html(matchID);
						// show Waiting for all Ready Modal
						$('#myModalWaitingForAllReady').modal({
							backdrop : "static",
							keyboard : false
						});
						// Countdown anwerfen
						$("#myModalWaitingForAllReady span.countdown").stop(true);
						$("#myModalWaitingForAllReady span.countdown").html("");
						waitingDauer = waitingTime4AllReady / 1000;
						$("#myModalWaitingForAllReady span.countdown").countDown({
							startNumber : waitingDauer,
							startFontSize : "20px",
							endFontSize : "20px",
						});
					}
					
					

					checkAllReadyForMatch2(matchID, 0, quickJoin, groupID, joinMode);
				});
			}
			else {
				l("accept Match failed");

			}
			l("acceptMatch2 End");
		}
	});

}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
var iterationTime = 2000;
var runnedTimeout = null;
function checkAllReadyForMatch2(matchID, runnedTime, quickJoin, groupID, joinMode) {
	l("Start checkAllReadyForMatch2");
	l("JOINMODE AY: "+joinMode);
	// wenn noch gewartet werden kann, dann nochmal suchen
	if (runnedTime <= waitingTime4AllReady) {

		$.ajax({
			url : 'ajax.php',
			type : "POST",
			dataType : 'json',
			data : {
				type : "match",
				mode : "checkAllReadyForMatch",
				ID : matchID
			},
			success : function(result) {
				l("getCountsOfReadyPlayer success");
				l(result);
				if (result.status == true) {

					switch(joinMode){
						case "1vs1Queue":
							badges = $("#waitingForAllReady1vs1 span[class*='badge']");
							l(badges);
							break;
						default:
							badges = $("#myModalWaitingForAllReady span[class*='badge']");
					}
					

					countReady = result.countReady;

					l("count:" + countReady);

					runnedTimeout = null;

					var mtid = result.matchTypeID;
					//l(runnedTimeout);
					l("hier test mTID:");
					l(mtid);
					l("ende MTID");
					
					switch(mtid){
						// wenn 1vs1 Queueu -> dann count 2 ausreichend
						case 8:
							// haben alle auf Ready geklickt?
							if (countReady == 2) {
								l("2 gefunden!");
								$('.modal').modal('hide');

								// timeout clearen
								clearTimeout(runnedTimeout);
								runnedTimeout = null;

								// Host von Lobby festlegen
								setLobbyHostForMatch(matchID);

								// Cookie f�r ein Tag setzen, dass Match
								// gefunden wurde, f�r weiterleitung
								$.cookie("matchFound", matchID, {
									expires : 360
								});

								// SeitenWarnung daktivieren
								setConfirmUnload(false);

								// Umleitung auf Match-Seite
								setTimeout(function() {
									window.location = "match.php?matchID=" + matchID;
									// l("UMLEITUNG");
								}, 1000);
							}

							// irgendjemand bereits abgebrochen
							else if (countReady == null) {
								// timeout clearen
								clearTimeout(runnedTimeout);
								runnedTimeout = null;
								l("DEINE MUDDA hier stehts doch:"+joinMode);
								kickFromQueue(matchID, "autoKickAfterAccept", quickJoin, false, groupID, joinMode);
							}
							// noch nciht alle auf Ready geklickt
							else {
								var i = 0;
								$(badges).each(function() {
									if (i < countReady) {
										$(this).attr("class", "badge badge-success");
									}
									i++;
								});
								// Sicht aktualisieren
								//$("#waitForOtherPlayersBadgeReady").html(result.html);

								// Rekursion und runnedTime erh�hen
								runnedTime += iterationTime;
								l("RT: "+runnedTime+" "+iterationTime);
								runnedTimeout = setTimeout(function() {
									l("mtID 8 1vs1Queue rekursion");
									checkAllReadyForMatch2(matchID, runnedTime, quickJoin, groupID, joinMode);
								}, iterationTime);

							}
							break;
						case 9:
							// haben alle auf Ready geklickt?
							if (countReady == 6) {
								l("6 gefunden!");
								$('.modal').modal('hide');

								// timeout clearen
								clearTimeout(runnedTimeout);
								runnedTimeout = null;

								// Host von Lobby festlegen
								setLobbyHostForMatch(matchID);

								// Cookie f�r ein Tag setzen, dass Match
								// gefunden wurde, f�r weiterleitung
								$.cookie("matchFound", matchID, {
									expires : 360
								});

								// SeitenWarnung daktivieren
								setConfirmUnload(false);

								// Umleitung auf Match-Seite
								setTimeout(function() {
									window.location = "match.php?matchID=" + matchID;
									// l("UMLEITUNG");
								}, 1000);
							}

							// irgendjemand bereits abgebrochen
							else if (countReady == null) {
								// timeout clearen
								clearTimeout(runnedTimeout);
								runnedTimeout = null;
								kickFromQueue(matchID, "autoKickAfterAccept", quickJoin, false, groupID, joinMode);
							}
							// noch nciht alle auf Ready geklickt
							else {

								// Sicht aktualisieren
								$("#waitForOtherPlayersBadgeReady").html(result.html);

								// Rekursion und runnedTime erh�hen
								runnedTime += iterationTime;

								runnedTimeout = setTimeout(function() {
									l("mtID 9 rekursion");
									checkAllReadyForMatch2(matchID, runnedTime, quickJoin, groupID, joinMode);
								}, iterationTime);
							}
							break;
						// alle anderen matchTypes 5vs5 und so
						case 1:
						default:
							// haben alle auf Ready geklickt?
							if (countReady == 10) {
								l("10 gefunden!");
								$('.modal').modal('hide');

								// timeout clearen
								clearTimeout(runnedTimeout);
								runnedTimeout = null;

								// MatchDetails einfuegen - geschiet jetzt
								// sofort nach dem ein match gefunden wurde
								// saveMatchDetails(matchID);

								// MatchTeams leeren
								// cleanMatchTeamsOfPlayer();

								// Host von Lobby festlegen
								setLobbyHostForMatch(matchID);

								// Cookie f�r ein Tag setzen, dass Match
								// gefunden wurde, f�r weiterleitung
								$.cookie("matchFound", matchID, {
									expires : 360
								});

								// SeitenWarnung daktivieren
								setConfirmUnload(false);

								// Umleitung auf Match-Seite
								setTimeout(function() {
									window.location = "match.php?matchID=" + matchID;
									// l("UMLEITUNG");
								}, 1000);
							}

							// irgendjemand bereits abgebrochen
							else if (countReady == null) {
								// timeout clearen
								clearTimeout(runnedTimeout);
								runnedTimeout = null;
								kickFromQueue(matchID, "autoKickAfterAccept", quickJoin, false, groupID, joinMode);
							}
							// noch nciht alle auf Ready geklickt
							else {

								// Badges einf�rben, je nachdem wie viele
								// Player
								// akzeptiert haben
								var i = 0;
								$(badges).each(function() {
									if (i < countReady) {
										$(this).attr("class", "badge badge-success");
									}
									i++;
								});
								// Sicht aktualisieren
								// $("#waitForOtherPlayersBadgeReady").html(
								// result.html);

								// Rekursion und runnedTime erh�hen
								runnedTime += iterationTime;

								runnedTimeout = setTimeout(function() {
									l("singleQueue5vs5 rekursion");
									checkAllReadyForMatch2(matchID, runnedTime, quickJoin, groupID, joinMode);
								}, iterationTime);

							}
					}		
				}
			}
		});

	}
	// zu lange gewartet, Match abbrechen, Player aus QUeue hauen und
	// MatchTeam/Match l�schen
	else {
		kickFromQueue(matchID, "autoKickAfterAccept", quickJoin, false, groupID, joinMode);
	}
	l("End checkAllReadyForMatch2");
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function cleanMatchTeamsOfPlayer() {
	l("cleanMatchTeamsOfPlayer Start");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "matchTeams",
			mode : "cleanMatchTeamsOfPlayer",
		},
		success : function(result) {
			l(result);
			l("cleanMatchTeams success");
			if (result.status == true) {
				l(result);
				l("MatchTeams-DB cleaned!");
			}
		}
	});
	l("cleanMatchTeamsOfPlayer End");
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function kickFromQueue(matchID, reason, quickJoin, cancelRejoin, groupID, joinMode) {
	l("kickFromQueue Start");
	$('.modal').modal('hide');
	$("#myModalReadyMatch span.countdown").html("");
	var redirect = true;
	// leaveQueue2();
	// cleanDBMatchmaking2(matchID);
	// deleteMatchDetails(matchID);
	// deleteCreatedMatch(matchID);
	//
	// kickAllPlayersOutOfQueue(matchID, reason);
	// cleanQueueGroup(groupID);
	// cleanHostForMatch(matchID);

	l(reason);
	switch (reason) {
		case "decline":
		case "autoKick":
			l("insertLock Start");
			$.ajax({
				url : 'ajax.php',
				type : "POST",
				dataType : 'json',
				data : {
					type : "QueueLock",
					mode : "insertLock"
				},
				success : function(result) {
					l("insertLock success");
					if (result.status == true) {
						l("added insertLock!");
					}
				}
			});
			l("insertLock End");
			break;
	}

	// SeitenWarnung daktivieren
	setConfirmUnload(false);

	l("joinMode:"+joinMode);
	var rejoin = "";
	if (cancelRejoin == false) {
		l("ey homo!");
		switch(joinMode){
			case "1vs1Queue":
				l("heyho  hier bin ich!");
				rejoin = "?rejoin=true&joinType=1vs1QueueJoin";
				break;
			case "singleQueue":
				l("QUICKJOIN:" + quickJoin);
				if (groupID > 0) {
					switch (quickJoin) {
						case true:
							rejoin = "";
							break;
						case false:
						default:
							rejoin = "?rejoin=true&joinType=duoQueueJoin&gid=" + groupID;
							break;

					}
				}
				else {
					switch (quickJoin) {
						case true:
							rejoin = "?rejoin=true&joinType=singleQueueQuickJoin";
							break;
						case false:
						default:
							rejoin = "?rejoin=true&joinType=singleQueueJoin";
							break;

					}
				}
				break;
		}
	}
	else {
		l("nee  du bist der homo!");
		var rejoin = "";
	}
	l("RJ:"+rejoin);
	l("start cleanEverything und so");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "matchmaking",
			mode : "cleanEverything",
			matchID : matchID,
			groupID : groupID,
			reason : reason
		},
		success : function(data) {
			l("test redirect");
			l(data);
			setTimeout(function() {
				l("Rejoin: "+rejoin);
				if (redirect) {
					window.location = "find_match.php" + rejoin;
				}
				l("test redirected!");
			}, (300));
		}
	});
	l("kickFromQueue End");
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function saveMatchDetails(matchID) {
	l("saveMatchDetails Start");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "match",
			mode : "saveMatchDetails",
			ID : matchID
		},
		success : function(result) {
			l("saveMatchDetails success");
			if (result.status == true) {
				l(matchID);
				l(result);
				l("saved MatchDetails!");
			}
		}
	});
	l("saveMatchDetails End");
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function cleanDBMatchmaking(matchID) {
	l("cleanDBMatchmaking Start");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "matchmaking",
			mode : "cleanDBMatchmaking",
			ID : matchID
		},
		success : function(result) {
			l("cleanDBMatchmaking success");
			if (result.status == true) {
				l(matchID);
				l("MatchTeams-DB cleaned!");
			}
		}
	});
	l("cleanDBMatchmaking End");
}

/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function deleteCreatedMatch(matchID) {
	l("deleteCreatedMatch Start");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "match",
			mode : "deleteCreatedMatch",
			ID : matchID
		},
		success : function(result) {
			l("deleteCreatedMatch success");
			if (result.status == true) {
				l(matchID);
				l("MatchTeams-DB cleaned!");
			}
		}
	});
	l("deleteCreatedMatch End");
}

/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function deleteMatchDetails(matchID) {
	l("deleteMatchDetails Start");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "matchDetails",
			mode : "deleteMatchDetails",
			ID : matchID
		},
		success : function(result) {
			l("deleteMatchDetails success");
			if (result.status == true) {
				l(matchID);
				l("MatchTeams-DB cleaned!");
			}
		}
	});
	l("deleteMatchDetails End");
}

/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function kickAllPlayersOutOfQueue(matchID, reason) {
	l("kickAllPlayersOutOfQueue Start");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "queue",
			mode : "kickAllPlayersOutOfQueue",
			ID : matchID,
			RS : reason
		},
		success : function(result) {
			l("kickAllPlayersOutOfQueue success");
			l(result);
			if (result.status == true) {
				l(matchID);
				l("All PLayer Kicked out of Queue with matchID: " + matchID + "!");
			}
		}
	});
	l("kickAllPlayersOutOfQueue End");
}

/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function setLobbyHostForMatch(matchID) {
	l("setLobbyHostForMatch Start");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "MatchDetailsHostLobby",
			mode : "setLobbyHostForMatch",
			ID : matchID
		},
		success : function(result) {
			l("setLobbyHostForMatch success");
			l(result);
			if (!result.status) {
				l(result);
			}
		}
	});
	l("setLobbyHostForMatch End");
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function cleanHostForMatch(matchID) {
	l("cleanHostForMatch Start");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "MatchDetailsHostLobby",
			mode : "cleanHostForMatch",
			ID : matchID
		},
		success : function(result) {
			l("cleanHostForMatch success");
			if (!result.status) {
				l(result);
			}
		}
	});
	l("cleanHostForMatch End");
}

/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function checkIfPlayerIsBanned() {
	l("checkIfPlayerIsBanned Start");
	var ret = null;
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "banlist",
			mode : "checkForBansOfPlayer"
		},
		success : function(result) {
			l("checkIfPlayerIsBanned success");
			l(result);
			if (result.banned) {
				ret = true;
			}
			else {
				ret = false;
			}
		}
	});
	l("checkIfPlayerIsBanned End");
	return ret;
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function cleanBansOfPlayer() {
	l("cleanBansOfPlayer Start");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "banlist",
			mode : "cleanBansOfPlayer"
		},
		success : function(result) {
			l("cleanBansOfPlayer success");
			l(result);
			if (!result.status) {
				l(result);
			}

		}
	});
	l("cleanBansOfPlayer End");
}
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function cleanQueueGroup(groupID) {
	l("cleanQueueGroup Start");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "QueueGroup",
			mode : "cleanQueueGroup",
			ID : groupID
		},
		success : function(result) {
			l("cleanQueueGroup success");
			l(result);
			if (!result.status) {
				l(result);
			}
		}
	});
	l("cleanQueueGroup End");
}

/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function cleanQueueGroup2(groupID) {
	l("cleanQueueGroup Start");
	var ret = $.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "QueueGroup",
			mode : "cleanQueueGroup",
			ID : groupID
		}
	});
	l("cleanQueueGroup End");
	return ret;
}

/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function checkAlreadyInMatchTeams() {
	l("checkAlreadyInMatchTeams Start");
	ret = $.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "matchTeams",
			mode : "checkAlreadyInMatchTeams"
		}
	});
	l("checkAlreadyInMatchTeams End");
	return ret;
}

/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function matchMakingSingleQueueLongPolling() {
	/*
	 * This requests the url "msgsrv.php" When it complete (or errors)
	 */
	$.ajax({
		type : "GET",
		url : "ajax.php",
		data : {
			type : "matchTeams",
			mode : "checkAlreadyInMatchTeams"
		},
		async : true, /*
						 * If set to non-async, browser shows page as
						 * "Loading.."
						 */
		cache : false,
		timeout : 20000, /* Timeout in ms */

		success : function(data) { /*
									 * called when request to barge.php
									 * completes
									 */
			addmsg("new", data); /*
									 * Add response to a .msg div (with the
									 * "new" class)
									 */
			setTimeout(waitForMsg, /* Request next message */
			1000 /* ..after 1 seconds */
			);
		},
		error : function(XMLHttpRequest, textStatus, errorThrown) {
			addmsg("error", textStatus + " (" + errorThrown + ")");
			setTimeout(waitForMsg, /* Try again after.. */
			15000); /* milliseconds (15seconds) */
		}
	});
};
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function playMatchFoundSound() {
	l("Start playMatchFoundSound");
	var mySound = new buzz.sound([ "files/sound/matchReadySound.ogg", "files/sound/matchReadySound.mp3", "files/sound/matchReadySound.wav", "files/sound/matchReadySound.aac" ]);
	mySound.play();
	mySound.setVolume(100);
	l(mySound.getVolume());
	l("End playMatchFoundSound");
}

function checkJoinQueue() {

	l("Start loadDuoTeams");
	var ret = $.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "queue",
			mode : "checkJoinQueue"
		}
	});
	l("End loadDuoTeams");
	return ret;
}

function joinSingleQueueFkt(quickJoin, groupID, justCM, joinMode){
	var spielmodi = null;
	var regions = null;
	if (quickJoin == true) {
		if (justCM) {
			m = new Array();
			m.push("3");
			spielmodi = m;
		}
		else {
			spielmodi = getQuickJoinSingleQueueModi();
		}
	}
	else {
		spielmodi = getSelectedSingleQueueModi();
	}
	regions = getRegion();

	$("#singleQueueArea p[class='text-error']").remove(); // Error
															// wieder
															// löschen

	if (spielmodi.length > 0 && regions.length > 0) {
		// beim Verlassen der Seite eine Warnung anzeigen:
		// aktivieren
		setConfirmUnload(true);

		// cookies für alle ausgewählte Spielmodi setzen
		$.each(spielmodi, function(index, value) {
			$.cookie("singleQueue[" + value + "]", true, {
				expires : 14
			});
		});

		$.ajax({
			url : 'ajax.php',
			type : "POST",
			dataType : 'json',
			data : {
				type : "queue",
				mode : "joinSingleQueue2",
				modi : spielmodi,
				region : regions
			},
			success : function(result) {
				l("joinSingleQueue success");
				l(result);
				// Uhr starten
				$('#matchMakingClock').stopwatch('start');
				l("Uhr gestartet");

				// matchmakingInfo generieren
				generateSingleQueueMatchMakingInfo(quickJoin, justCM);

				// matchDetails leeren
				$("#matchmakingDetails").html("");

				// Modal öffnen und MAtchmaking starten
				$('#myModalMatchMaking').modal({
					backdrop : "static",
					keyboard : false
				}).css({
					width : '81%',
					'margin-left' : function() {
						return -($(this).width() / 2);
					}
				});

				// matchmaking anstoßen
				doSingleQueueMatchMaking3(spielmodi, regions, quickJoin, groupID, joinMode);
			}
		});
	}
	// Fehlerbehandlung Matchmodes
	else {
		if (spielmodi.length == 0) {
			error = '<p class="text-error">Select at least one Matchmode!</p>';
			$("#SingleQueueMatchModesErrors").append(error);
		}
	}
}