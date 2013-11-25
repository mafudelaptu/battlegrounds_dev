var waitingTime4AcceptReady = 40000;
var timeout;
var waitingTime4AllReady = 40000;
var intervalID = null;
var cancelTimeoutID = null;
var runnedTime = 0;
var timePulling = 3000;

/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function join1vs1Queue2() {
	l("join1vs1Queue Start");

	// Carousel stoppen
	//$("#1vs1QueueCarousel").carousel('pause');

	checkData = checkJoinQueue();
	checkData.success(function(checkResult) {
		l(checkResult);
		switch (checkResult.status) {
			case true:
				join1vs1QueueFkt();
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
				if (rejoin != "true") {
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

						bootbox.alert(text, function() {
							join1vs1QueueFkt();
						});
					}
					else {
						if (banCounts == 1) {
							var text = "<div align='center'><h4>You got one active warn!</h4>"
									+ "<p>For the next warn you get a 24 hours Prison-Queue-Time. Please be more mannered in future.</p></div>";

							bootbox.alert(text, function() {
								// cleanBansOfPlayer();
								join1vs1QueueFkt();
							});
						}
						else {
							var text = "<div align='center'><h4>you are not banned anymore!</h4>" + "<p>Now you can join the Normal-Queue again!</p></div>";

							bootbox.alert(text, function() {
								join1vs1QueueFkt();
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
		leaveQueue2(null);
	});
	l("checkIfPlayerIsBanned End");
}

function join1vs1QueueFkt() {
	// wenn nicht gebannt
	l("nicht gebannt!");
	// Selektierte Spielmodi auslesen
	var spielmodi = null;
	var regions = null;
	// if (quickJoin == true) {
	// spielmodi = getQuickJoin1vs1QueueModi();
	// regions = getRegion();
	// } else {
	spielmodi = getSelected1vs1QueueModi();
	regions = getRegion();
	// }
	
	// Chat initialisieren
	initChat("findMatch", $.datepicker.formatDate('yy/mm/dd', new Date()), "findMatchChat"); 
	
	$("#1vs1QueueArea p[class='text-error']").remove(); // Error wieder löschen

	if (spielmodi.length > 0 && regions.length > 0) {

		//
		// beim Verlassen der Seite eine Warnung anzeigen:
		// aktivieren
		setConfirmUnload(true);

		$.each(spielmodi, function(index, value) {
			$.cookie("1vs1Queue[" + value + "]", true, {
				expires : 14
			}); // cookies für alle ausgewählten Spielmodi
			// setzen
		});

		// Elo Werte bereits vorhanden
		$.ajax({
			url : 'ajax.php',
			type : "POST",
			dataType : 'json',
			data : {
				type : "queue",
				mode : "join1vs1Queue",
				modi : spielmodi,
				region : regions
			},
			success : function(result) {
				l("join1vs1Queue success");
				l(result);
						// Uhr starten
						$('#matchMakingClock').stopwatch('start');
						l("Uhr gestertet");
						// matchmakingInfo generieren
						generate1vs1QueueMatchMakingInfo();

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

						// matchmaking
						// anstoßen
						do1vs1QueueMatchMaking(spielmodi, regions);
			}
		});
		l("checkIfUserAlreadyHave1vs1Stats End");

	}
	// Fehlerbehandlung
	else {
		if (spielmodi.length == 0) {
			error = '<p class="text-error">Select at least one Matchmode!</p>';
			$("#1vs1QueueMatchModesErrors").append(error);
		}
		if (regions.length == 0) {
			error = '<p class="text-error">Select at least one Region!</p>';
			$("#1vs1QueueRegionErrors").append(error);
		}

	}
}

/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
 */
function do1vs1QueueMatchMaking(spielmodi, regions) {
	l("Start do1vs1QueueMatchMaking");
	// set join Mode
	var joinMode = "1vs1Queue";
	
	l("JOINMODE:"+joinMode);
	
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
				mode : "oneVsOneQueueSearch",
				modi : spielmodi,
				region : regions,
				forceSearch : force
			},
			success : function(result) {
				l(result);

				update1vs1QueuePlayersFound(result.queue);
				updateRange(result.range);
				updateUserPool(result.skillBracket);
				updateNextMatchmakingTime(result.nextMatchmaking);
				updateQueueStats(result.queueCounts);
				
				if (result.status == "searching") {
					doMatchmakingTimeout = setTimeout(function() {
						do1vs1QueueMatchMaking(spielmodi, regions);
					}, timePulling);
				}
				else if (result.status == "finished") {

					clearTimeout(doMatchmakingTimeout);
					doMatchmakingTimeout = null;

					// match found Sound abspielen
					playMatchFoundSound();

					// title blinken lassen wenn match geufnden
					$.titleAlert("Match found!", {
						stopOnFocus : true,
						duration : 0,
						interval : 500
					});

					setTimeout(function() {
						// aktuelles Modal schlie�en und ReadyMatchModal
						// �ffnen
						$("#myModalMatchMaking").modal("hide");
						matchWasFoundModal2(result.matchID, false, null, joinMode);
					}, 1000);
				}
				else if (result.status == "notInQueue") {
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
		});
	}
	l("End do1vs1QueueMatchMaking");
}
