var waitingTime4AcceptReady = 40000;
var timeout;
var waitingTime4AllReady = 40000;
var intervalID = null;
var cancelTimeoutID = null;
var runnedTime = 0;
var timePulling = 3000;

function join3vs3Queue(quickJoin) {
	l("join3vs3Queue Start");

	// Carousel stoppen
	$("#3vs3QueueCarousel").carousel('pause');

	// Queue leeren
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

	// Checken ob Spieler gebannt ist
	l("checkIfPlayerIsBanned Start");
	$
			.ajax({
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
					if (result.banned == true
							|| (banCounts == 1 && display == 1)) {

						l("gebannten Spieler behandeln");
						// gebannten Spieler behandeln
						$
								.ajax({
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
											bannedAt = new Date(
													bannedAtTimestamp * 1000)
													.format('d.m.Y - h:i:s');
											bannedTill = new Date(
													bannedTillTimestamp * 1000)
													.format('d.m.Y - h:i:s');
											l(bannedTill);

											bannedBy = result2.data.Reason;

											text = "<div align='center'><h4>You got banned "
													+ bannedBy
													+ "</h4> "
													+ "<p>at "
													+ bannedAt
													+ " till "
													+ bannedTill
													+ "</p>"
													+ "<h3>It is your "
													+ banCounts
													+ ". Ban!</h3>"
													+ "<p>Until then you cant join a Queue! Try be more mannered next time.</p></div>";

											bootbox.alert(text, function() {

											});
										} else {
											if (banCounts == 1) {
												text = "<div align='center'><h4>You got the first time banned!</h4>"
														+ "<p>Now it is just a warning!</p>"
														+ "<p>Next time you get a 24 hours ban. Please be more mannered in future.</p></div>";

												bootbox.alert(text, function() {
													cleanBansOfPlayer();
												});
											} else {
												text = "<div align='center'><h4>you are not banned anymore!</h4>"
														+ "<p>Now you can join a Queue again!</p></div>";

												bootbox.alert(text, function() {
													cleanBansOfPlayer();
												});

												l(result2);
											}
										}
									}
								});
					} else {
						// wenn nicht gebannt
						l("nicht gebannt!");
						// Selektierte Spielmodi auslesen
						var spielmodi = null;
						var regions = null;
						if (quickJoin == true) {
							spielmodi = getQuickJoin3vs3QueueModi();
							regions = getRegion();
						} else {
							spielmodi = getSelected3vs3QueueModi();
							regions = getRegion();
						}

						$(
								"#3vs3QueueArea p[class='text-error']")
								.remove(); // Error wieder löschen

						if (spielmodi.length > 0 && regions.length > 0) {

							//
							// beim Verlassen der Seite eine Warnung anzeigen:
							// aktivieren
							setConfirmUnload(true);

							$.each(spielmodi, function(index, value) {
								$.cookie("3vs3Queue[" + value + "]", true, {
									expires : 14
								}); // cookies für alle ausgewählten Spielmodi
								// setzen
							});

							// kontrollieren ob Elo schon in diesem MatchType
							// vorhanden ist, sonst eintragen
							l("checkIfUserAlreadyHave3vs3Stats Start");
							matchTypeID = 9;
							$
									.ajax({
										url : 'ajax.php',
										type : "POST",
										dataType : 'json',
										data : {
											type : "userElo",
											mode : "checkIfUserHaveEloForMatchType",
											ID : matchTypeID
										},
										success : function(result) {
											l("checkIfUserAlreadyHave3vs3Stats success");
											l(result);
											if (!result.status) {
												// f�r 3vs3Queue gibts noch
												// keine Elo Werte
												l("insert3vs3EloValues Start");
												$
														.ajax({
															url : 'ajax.php',
															type : "POST",
															dataType : 'json',
															data : {
																type : "userElo",
																mode : "insertFirstEloForMatchType",
																ID : matchTypeID
															},
															success : function(
																	result) {
																l("insert3vs3EloValues success");
																l(result);
																if (!result.status) {
																	l(result);
																}
																join3vs3Queue(quickJoin);
															}
														});
												l("insert3vs3EloValues End");
											} else {
												// Elo Werte bereits vorhanden
												$
														.ajax({
															url : 'ajax.php',
															type : "POST",
															dataType : 'json',
															data : {
																type : "queue",
																mode : "join3vs3Queue",
																modi : spielmodi,
																region : regions
															},
															success : function(
																	result) {
																l("join3vs3Queue success");
																l(result);

																if (result.status != "inMatch") {

																	setTimeout(
																			function() {
																				// Uhr
																				// starten
																				$(
																						'#matchMakingClock')
																						.stopwatch(
																								'start');
																				l("Uhr gestertet");
																				// matchmakingInfo
																				// generieren
																				generate3vs3QueueMatchMakingInfo(quickJoin);

																				// matchDetails
																				// leeren
																				$(
																						"#matchmakingDetails")
																						.html(
																								"");

																				// Modal
																				// öffnen
																				// und
																				// MAtchmaking
																				// starten
																				$(
																						'#myModalMatchMaking')
																						.modal(
																								{
																									backdrop : "static",
																									keyboard : false
																								})
																						.css(
																								{
																									width : '81%',
																									'margin-left' : function() {
																										return -($(
																												this)
																												.width() / 2);
																									}
																								});

																				// matchmaking
																				// anstoßen
																				do3vs3QueueMatchMaking(
																						spielmodi,
																						regions,
																						quickJoin);
																			},
																			400);

																} else {
																	l("already in Match!");
																	bootbox
																			.alert(
																					"You are already in a Match! Please check your notifications! (top right)",
																					function() {
																						// beim
																						// Verlassen
																						// der
																						// Seite
																						// eine
																						// Warnung
																						// anzeigen:
																						// aktivieren
																						setConfirmUnload(false);

																						setTimeout(
																								function() {
																									window.location = "find_match.php";
																								},
																								100);
																					});
																}

															}
														});

											}

										}
									});
							l("checkIfUserAlreadyHave3vs3Stats End");

						}
						// Fehlerbehandlung
						else {
							if (spielmodi.length == 0) {
								error = '<p class="text-error">Select at least one Matchmode!</p>';
								$("#3vs3QueueMatchModesErrors").append(error);
							}
							if (regions.length == 0) {
								error = '<p class="text-error">Select at least one Region!</p>';
								$("#3vs3QueueRegionErrors").append(error);
							}

						}

					}
				}
			});
	// Leave Queue Button onlick
	$("#singleQueueLeaveQueueButton").click(function() {
		leaveQueue2(null);
	});
	l("checkIfPlayerIsBanned End");
}

function do3vs3QueueMatchMaking(spielmodi, regions, quickJoin) {
	// Prüfen ob spieler schon mit anderen gematchmaked wurde
	alreadyMatchMaked = false;

	// auslesen ob forceSearch checkbox activiert wurde
	forceChecked = $("#forceSearching").attr("checked");

	if (forceChecked == "checked") {
		force = true;
	} else {
		force = false;
	}
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
				mode : "threeVsThreeQueueSearch",
				modi : spielmodi,
				region : regions,
				forceSearch : force
			},
			success : function(result) {
				l(result);

				update3vs3QueuePlayersFound(result.queue);

				updateRange(result.range);

				if (result.status == "searching") {
					doMatchmakingTimeout = setTimeout(function() {
						do3vs3QueueMatchMaking(spielmodi, regions, quickJoin);
					}, timePulling);
				} else if (result.status == "finished") {

					clearTimeout(doMatchmakingTimeout);
					doMatchmakingTimeout = null;

					// match found Sound abspielen
					$.playSound("files/sound/matchReadySound.mp3");
					
					// title blinken lassen wenn match geufnden
					$.titleAlert("Match found!", {
					    stopOnFocus:true,
					    duration:0,
					    interval:500
					});
					
					setTimeout(function() {
						// aktuelles Modal schlie�en und ReadyMatchModal
						// �ffnen
						$("#myModalMatchMaking").modal("hide");
						matchWasFoundModal2(result.matchID, quickJoin, null);
					}, 1000);
				}
			}
		});
	}
}
