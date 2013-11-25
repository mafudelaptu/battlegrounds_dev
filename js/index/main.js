/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
$(document).ready(
		function() {
			l(document.URL);
			if (document.URL.indexOf("dota2dev.") >= 0 || document.URL.indexOf("battlegrounds") >= 0) {
				// Handler for .ready() called.
				$('#upcomingEvents2').block({
					message : 'in work',
				});

				// Onclicks f�r Best Players
				initOnclicksForBestPLayers();

				// Onclicks f�r last MatchMatches
				initOnclicksForLastMatches();
				
				$("#homeListRotator").wtListRotator({
					list_align:"right",
					scroll_type:"mouse_over",
				});
				
			}
		});
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function initOnclicksForBestPLayers() {

	// Options auslesen
	var select = $("#bestPlayersSelectCategory");
	l(select);

	select.change(function() {
		optionVal = $(this).find(':selected').val();

		// loading screen
		$('#bestPlayersTable').block({
			message : 'fetching data',
		});

		l("getBestPlayersFetchedData Start");
		$.ajax({
			url : 'ajax.php',
			type : "POST",
			dataType : 'json',
			data : {
				type : "ladder",
				mode : "getBestPlayersFetchedData",
				mmID : optionVal
			},
			success : function(result) {
				l("getBestPlayersFetchedData success");
				l(result);
				if (result.status) {
					$("#bestPlayersTable").html(result.data);
				}

			}
		});
		l("getBestPlayersFetchedData End");
		$.unblockUI();
	});
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function initOnclicksForLastMatches() {

	// Options auslesen
	var select = $("#lastMatchesPlayedSelectCategory");
	l(select);

	select.change(function() {
		
		optionVal = $(this).find(':selected').val();

		// loading screen
		$('#lastMatchesPlayedTable').block({
			message : 'fetching data',
		});

		l("getLastPlayedMatches Start");
		$.ajax({
			url : 'ajax.php',
			type : "POST",
			dataType : 'json',
			data : {
				type : "match",
				mode : "getLastPlayedMatches",
				mmID : optionVal
			},
			success : function(result) {
				l("getLastPlayedMatches success");
				l(result);
				if (result.status) {
					$("#lastMatchesPlayedTable").html(result.data);
				}

			}
		});
		l("getLastPlayedMatches End");
		$.unblockUI();
	});

}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function joinEvent() {
	var idJoinButton = "#joinEventButton";
	var disabled = $(idJoinButton).hasClass('disabled');
	if (!disabled) {
		l("initOnclickJoinEvent Start");

		var eventID = $(idJoinButton).attr("data-value");
		l(eventID);
		// timestamp kontrollieren ob er sich noch einschreiben darf
		var endTimestamp = $(idJoinButton).attr("data-time");
		l(endTimestamp);
		var now = $.now() / 1000;
		l(now);
		
		if (now <= endTimestamp) {
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
											success : function(
													result2) {
												l("getCurrentBanDataOfPlayer success");
												l(result2);
												l("BANS:"
														+ banCounts);
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

													bannedBy = result2.data.Reason+" - <img src='"+result2.data.Avatar+"'><a href='profile.php?ID="+result2.data.BannedBySteamID+"'>"+result2.data.Name+"</a>";
													
													banReasonText = result2.data.BanReasonText;
													text = "<div align='center'><h4>You got banned "
															+ bannedBy
															+ "</h4> "
															+ "<p>at "
															+ bannedAt
															+ " till "
															+ bannedTill
															+ "</p>"
															+"<p class='well'>"
															+ banReasonText
															+"</p>"
															+ "<h3>It is your "
															+ banCounts
															+ ". Ban!</h3>"
															+ "<p>Until then you cant join a Queue! Try be more mannered next time.</p></div>";

													bootbox
															.alert(
																	text,
																	function() {

																	});
												} else {
													if (banCounts == 1) {
														text = "<div align='center'><h4>You got the first time banned!</h4>"
																+ "<p>Now it is just a warning!</p>"
																+ "<p>Next time you get a 24 hours ban. Please be more mannered in future.</p></div>";

														bootbox
																.alert(
																		text,
																		function() {
																			cleanBansOfPlayer();
																		});
													} else {
														text = "<div align='center'><h4>you are not banned anymore!</h4>"
																+ "<p>Now you can join a Queue again!</p></div>";

														bootbox
																.alert(
																		text,
																		function() {
																			cleanBansOfPlayer();
																		});

														l(result2);
													}
												}
											}
										});
							} else {
								$
								.ajax({
									url : 'ajax.php',
									type : "POST",
									dataType : 'json',
									data : {
										type : "EventSubmissions",
										mode : "joinEvent",
										ID : eventID
									},
									success : function(result) {
										l("initOnclickJoinEvent success");
										l(result);
										if(result.status){
											text = "<div align='center'><h4>You sucessfully singed-in to this Event</h4> "
												+ "<p>you get a notification (top-right) if the Event is open. Then you can start and play your first Matches.</p>";
										bootbox.alert(text, function() {
											window.location.reload();
										});
										}
										else{
											text = "<div align='center'><h4 class='text-error'>You cant join this Event</h4> "
												+ "<p>you do not fulfill the requirements</p>";
										bootbox.alert(text, function() {
											window.location.reload();
										});
										}
										
									}
								});

							}
						}
						});
			
		}
		// sonst anzeigen das er zu sp�t ist
		else {
			text = "<div align='center'><h4>Sorry, you are too late :(</h4> "
					+ "<p>you cant sign-in to this Event anymore. Try to be faster next time :)</p>";
			bootbox.alert(text, function() {
				window.location.reload();
			});
		}

	}

	l("initOnclickJoinEvent End");
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function signOutOfEvent() {

	idSingOutButton = "#signOutEventButton";
	l($(idSingOutButton));

	l("initOnclickSignOutEvent Start");

	var eventID = $(idSingOutButton).attr("data-value");
	l(eventID);
	// timestamp kontrollieren ob er sich noch einschreiben
	// darf
	var endSubmissionTimestamp = $(idSingOutButton).attr("data-time");
	// l(startEvent);
	var now = $.now() / 1000;
	l(now);
	if (now < endSubmissionTimestamp) {
		$
				.ajax({
					url : 'ajax.php',
					type : "POST",
					dataType : 'json',
					data : {
						type : "EventSubmissions",
						mode : "singOutPlayerOfEvent",
						ID : eventID
					},
					success : function(result) {
						l("initOnclickSignOutEvent success");
						text = "<div align='center'><h4>You sucessfully singed-out of this Event</h4> "
								+ "";
						bootbox.alert(text, function() {
							window.location.reload();
						});
					}
				});

	}
	// zu sp�t darf nciht mehr sing outen
	else {
		text = "<div align='center'><h4>Sorry, you are too late :(</h4> "
				+ "<p>you cant sign-out of this Event anymore. Now you have to play the Event!</p>";
		bootbox.alert(text, function() {
			window.location.reload();
		});
	}

	l("initOnclickSignOutEvent End");
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function goToEvent(button){
	//var idGoToButton = "#goToEventButton";
	var idGoToButton = "#"+button;
	var eventID = $(idGoToButton).attr("data-value");
	var createdEventID = $(idGoToButton).attr("data-value2");
	l(eventID);
	l(createdEventID);
	if(createdEventID > 0){
		window.location = "event.php?eventID="+eventID+"&cEID="+createdEventID;
	}
	else{
		
	}
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function showSignedInPlayers(){
	l("showSignedInPlayers Start");
	id = "#showSignedInPlayersModal";
	
	$(id).modal("show");
	l("showSignedInPlayers End");
}
