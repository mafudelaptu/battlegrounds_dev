/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
$(document).ready(
		function() {
			if (getParameterByName("matchID") > 0) {
				
				// onclick events Definieren

				// normale up und DownVotes onclicks
				$("button.normalVote").click(
						function() {
							
							// SteamID auslesen von ihm
							var steamID = $(this).val();

							// Typ des Submits
							var type = $(this).attr("data-type");

							// ID VoteType
							var id = $(this).attr("data-value");
							
							// Label
							var label = $(this).attr("data-label");
							
							// VoteCountValue
							value = getVoteCount(type);
							if(value > 0){
								l(steamID+" "+type+" "+id);
								insertVote(steamID, type, id, getParameterByName("matchID"));
								
								// Disable Buttons
								disableVoteButtons($(this), label, type);
								
								// Info Vote COunt decreeasen
								decreaseVoteCount(type);
							}
							else{
								switch(type){
								case "1":
									bootbox.alert("Your Upvote-Count is zero! You can't Upvote a Player anymore for this week", function() {
										  
									});
								break;
								case "-1":
									bootbox.alert("Your Downvote-Count is zero! You can't Downvote a Player anymore for this week", function() {
										  
									});
									break;
								}
							}
							
						});

				// Spezielle Up und DOwnvote onclicks
				$(".btn-group.voteButtonGroup > .dropdown-menu > li > a")
						.click(function() {
							// SteamID auslesen von ihm
							var steamID = $(this).attr("data-name");

							// Typ des Submits
							var type = $(this).attr("data-type");

							// ID VoteType
							var id = $(this).attr("data-value");
							
							// Label
							var label = $(this).html();
							
							// VoteCountValue
							value = getVoteCount(type);
							if(value > 0){
								l(steamID+" "+type+" "+id);
								insertVote(steamID, type, id, getParameterByName("matchID"));
								
								// Disable Buttons
								disableVoteButtons($(this), label, type);
								
								// Info Vote COunt decreeasen
								decreaseVoteCount(type);
							}
							else{
								switch(type){
								case "1":
									bootbox.alert("Your Upvote-Count is zero! You can't Upvote a Player anymore for this week", function() {
										  
									});
								break;
								case "-1":
									bootbox.alert("Your Downvote-Count is zero! You can't Downvote a Player anymore for this week", function() {
										  
									});
									break;
								}
								
							}
							
							
						});

			}
		});
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function insertVote(steamID, type, id, matchID) {
	l("Start insertVote");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "UserVotes",
			mode : "insertVote",
			sID : steamID,
			mID : matchID,
			vtID : id,
			t: type
		},
		success : function(result) {
			l(result);
			
		}
	});
	l("End insertVote");
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function disableVoteButtons(elem, label, type){
	
	switch(type){
	case "1":
		style = "color:#5bb75b";
	break;
	case "-1":
		style = "color:#da4f49";
		break;
	}
	
	html = "<span class='t' title='successfully Voted!' style='"+style+"'>&nbsp;<i class='icon-ok-circle' ></i> "+label+"</span>";
	$(elem).closest("div.voteSection").html(html);
	
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function decreaseVoteCount(type){
	value = getVoteCount(type);

	if(value > 0){
		value = value - 1;
		$(elem).html(value);
	}
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function getVoteCount(type){
	switch(type){
	case "1":
		elem = "#userUpvotesLeft";

	break;
	case "-1":
		elem = "#userDownvotesLeft";
		break;
	}
	value = parseInt($(elem).html());
	
	return value;
}