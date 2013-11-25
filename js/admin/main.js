/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
$(document).ready(
		function() {
			l(document.URL);
			if (document.URL.indexOf("/banPanel.php") >= 0) {
				$("#bannedPlayersTable").dataTable({"destroy":true});
				$("#permaBannedPlayersTable").dataTable({"destroy":true});
			}
		});
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function fakeSubmittsSimulieren(){
	matchID = $("#fakeSubmittsMatchID").val();
	$("#fakeSubmittsResposne").html("");
	teamWonID = $("input[name='fakeSubmittsTeamWon']:checked").val();
	l(teamWonID);
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "fakeUser",
			mode : "fakeSubmittsSimulieren",
			ID: matchID,
			teamWon: teamWonID
		},
		success : function(result) {
			l(result);
			$("#DEBUG_AREA").html(result.debug);
			$("#fakeSubmittsResposne").html("Eingetragen!");
		}
	});
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function resetSubmissions(){
	matchID = $("#fakeSubmittsMatchID").val();
	$("#resetSubmissionsResposne").html("");
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "fakeUser",
			mode : "resetSubmissions",
			ID: matchID
		},
		success : function(result) {
			l(result);
			$("#DEBUG_AREA").html(result.debug);
			$("#resetSubmissionsResposne").html("Einträge zurückgesetzt für MatchID: "+matchID+" ("+result.status+")");
		}
	});
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function cronjobMatchResultsSubmit(){
$("#cronjobMatchResultsSubmitResponse").html("");
	
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "cronjobs",
			mode : "cronjobMatchResultsSubmit",
		},
		success : function(result) {
			l(result);
			$("#DEBUG_AREA").html(result);
			$("#cronjobMatchResultsSubmitResponse").html("Status: true");
		}
	});
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function cronjobDoAllCronjobs(){
	$("#cronjobDoAllCronjobsResponse").html("");
		
		$.ajax({
			url : '../ajaxAdmin.php',
			type : "POST",
			dataType : 'json',
			data : {
				type : "cronjobs",
				mode : "cronjobDoAllCronjobs",
			},
			success : function(result) {
				l(result);
				$("#DEBUG_AREA").html(result);
				$("#cronjobDoAllCronjobsResponse").html("Status: true");
			}
		});
	}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function insertUserAdmin(){
	steamID = $("#insertUserSteamID").val();
	$("#insertUserSteamIDResposne").html("");
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "fakeUser",
			mode : "insertUser",
			ID: steamID
		},
		success : function(result) {
			l(result);
			$("#DEBUG_AREA").html(result.debug);
			$("#insertUserResposne").html("("+result.status+")");
		}
	});
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function insertRandomUserinQueue(){
	$("#insertRandomUserinQueueResposne").html("");
	matchTypeID = $("#insertUserMatchTypeID").val();
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "fakeUser",
			mode : "insertRandomUserinQueue",
			ID : matchTypeID
		},
		success : function(result) {
			l(result);
			$("#DEBUG_AREA").html(result.debug);
			$("#insertRandomUserinQueueResposne").html("("+result.status+")");
		}
	});
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function submitAllMatchAccept(){
	$("#submitAllMatchAcceptResposne").html("");
	
	matchID = $("#submitAllMatchAcceptMatchID").val();
	
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "fakeUser",
			mode : "submitAllMatchAccept",
			ID: matchID
		},
		success : function(result) {
			l(result);
			$("#DEBUG_AREA").html(result.debug);
			$("#submitAllMatchAcceptResposne").html("("+result.status+")");
		}
	});
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function insertTestMatches(){
$("#insertTestMatchesResponse").html("");
	
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "fakeMatches",
			mode : "insertRandomTestMatches"
		},
		success : function(result) {
			l(result);
			$("#DEBUG_AREA").html(result.debug);
			$("#insertTestMatchesResponse").html("("+result.status+")");
		}
	});
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function insertNewMatchMode(){
$("#insertNewMatchModeResponse").html("");
	
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "matchMode",
			mode : "insertNewMatchMode"
		},
		success : function(result) {
			l(result);
			$("#DEBUG_AREA").html(result.debug);
			$("#insertTestMatchesResponse").html("("+result.status+")");
		}
	});
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function addBanToPlayer(){
$("#banSteamIDResponse").html("");

 	var steamID = $("#banSteamID").val();
 	var reason = $("#BanReasonText").val();
 	
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "banlist",
			mode : "insertBan",
			ID: steamID,
			reason: reason
		},
		success : function(result) {
			l(result);
			$("#DEBUG_AREA").html(result.debug);
			$("#banSteamIDResponse").html("("+result.status+")");
			$("#bannedPlayersTable").html("");
			//Update Table
			$.ajax({
				url : '../ajaxAdmin.php',
				type : "POST",
				dataType : 'json',
				data : {
					type : "banlist",
					mode : "getBannedPlayers"
				},
				success : function(result) {
					var html = result.tableHTML;
					$("#bannedPlayersTable").dataTable().fnDestroy();
					$("#bannedPlayersTable").html(html);
					$("#bannedPlayersTable").dataTable();
				}
			});
		}
	});
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function disableLastBan(){
	$("#banSteamIDResponse").html("");

	 	steamID = $("#banSteamID").val();
	 	
		$.ajax({
			url : '../ajaxAdmin.php',
			type : "POST",
			dataType : 'json',
			data : {
				type : "banlist",
				mode : "disableLastBan",
				ID: steamID
			},
			success : function(result) {
				l(result);
				$("#DEBUG_AREA").html(result.debug);
				$("#banSteamIDResponse").html("("+result.status+")");
				$("#bannedPlayersTable").html("");
				//Update Table
				$.ajax({
					url : '../ajaxAdmin.php',
					type : "POST",
					dataType : 'json',
					data : {
						type : "banlist",
						mode : "getBannedPlayers"
					},
					success : function(result) {
						var html = result.tableHTML;
						$("#bannedPlayersTable").dataTable().fnDestroy();
						$("#bannedPlayersTable").html(html);
						$("#bannedPlayersTable").dataTable();
					}
				});
			}
		});
	}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function removeLastBan(){
	$("#banSteamIDResponse").html("");

	 	steamID = $("#banSteamID").val();
	 	
		$.ajax({
			url : '../ajaxAdmin.php',
			type : "POST",
			dataType : 'json',
			data : {
				type : "banlist",
				mode : "removeLastBan",
				ID: steamID
			},
			success : function(result) {
				l(result);
				$("#DEBUG_AREA").html(result.debug);
				$("#banSteamIDResponse").html("("+result.status+")");
				$("#bannedPlayersTable").html("");
				//Update Table
				$.ajax({
					url : '../ajaxAdmin.php',
					type : "POST",
					dataType : 'json',
					data : {
						type : "banlist",
						mode : "getBannedPlayers"
					},
					success : function(result) {
						var html = result.tableHTML;
						$("#bannedPlayersTable").dataTable().fnDestroy();
						$("#bannedPlayersTable").html(html);
						$("#bannedPlayersTable").dataTable();
					}
				});
			}
		});
	}

function deletePermaBan(steamID){
	l("Start deletePermaBan");
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "banlist",
			mode : "deletePermaBan",
			steamID : steamID
		},
		success : function(result) {
			l("deletePermaBan success");
			l(result);
			if (result.status !== true) {
				
			}
			else {
				var text = "<div align='center'><h4>Successfully deleted the permaban of player</h4></div>";

				bootbox.alert(text, function(){
					window.location = "banPanel.php";
				});
			}
		}
	});
	l("End deletePermaBan");
}