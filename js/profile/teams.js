/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function createDuoTeam(){
	partnerSteamID = $("input[name='createGroupSelection']:checked").val();
	teamName = $("#duoTeamName").val();
	$("#createDuoTeamError").html("").removeClass("alert").removeClass("alert-error").removeClass("alert-success").hide();
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "group",
			mode : "createDuoGroup",
			partnerID: partnerSteamID,
			name: teamName
		},
		success : function(result3) {
			l("createDuoGroup success");
			l(result3);
			if(result3.status !== true){
				$("#createDuoTeamError").html(result3.status).addClass("alert alert-error").show();
			}
			else{
				$("#createDuoTeamError").html("Team successfully created!").addClass("alert alert-success").show();
			}
			
			loadDuoTeams();
			loadDuoOpenTeams();
		}
	});
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function loadDuoTeams(){
	l("Start loadDuoTeams");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "group",
			mode : "getGroupsOfUser2"
		},
		success : function(result) {
			l("loadDuoTeams success");
			l(result);
			$("#duoTeamList").html(result.html);
		}
	});
	l("End loadDuoTeams");
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function loadDuoOpenTeams(){
	l("Start loadDuoTeams");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "group",
			mode : "getOpenTeams"
		},
		success : function(result) {
			l("loadDuoTeams success");
			l(result);
			$("#openTeamsList").html(result.html);
		}
	});
	l("End loadDuoTeams");
}

/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function loadTeamInvites(){
	l("Start loadTeamInvites");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "group",
			mode : "loadTeamInvites"
		},
		success : function(result) {
			l("loadTeamInvites success");
			l(result);
			$("#teamInvitesArea").html(result.html);
		}
	});
	l("End loadTeamInvites");
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function deleteTeam(id, mode){
	text = "<p>Are you sure you want to delete the Team #"+id+"?</p>";
	bootbox.confirm(text, function(result) {
		if(result === true){
			l("Start deleteTeam");
			$.ajax({
				url : 'ajax.php',
				type : "POST",
				dataType : 'json',
				data : {
					type : "group",
					mode : "deleteTeam",
					ID: id
				},
				success : function(result) {
					l("deleteTeam success");
					l(result);
					if(result.status === true){
						text = "<p>You successfully deleted the Team #"+id+"</p>";
						bootbox.alert(text, function(){
							switch(mode){
								case 1:
										loadDuoTeams();
									break;
								case 2:
									break;
							}
						});
					}
					else{
						text = "<p>There went something wrong. Could not delete the Team #"+id+"!</p>";
						bootbox.alert(text);
					}
					
				}
			});
			l("End deleteTeam");
		}
	});
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function editTeamName(id, mode, val){
	var text = "Edit the Teamname:";
	bootbox.prompt(text, "Cancel", "Edit Teamname!", function(result) {
		if(result !== null){
			l("Start editTeamName");
			$.ajax({
				url : 'ajax.php',
				type : "POST",
				dataType : 'json',
				data : {
					type : "group",
					mode : "editTeamName",
					name: result,
					ID: id
				},
				success : function(result) {
					l("editTeamName success");
					l(result);
					if(result.status === true){
						text = "<p>You successfully edited the Teamname of Team #"+id+"</p>";
						bootbox.alert(text, function(){
							switch(mode){
								case 1:
										loadDuoTeams();
									break;
								case 2:
									break;
							}
						});
					}
					else{
						text = "<p>There went something wrong. Could not edit the Teamname of Team #"+id+"!</p>";
						bootbox.alert(text);
					}
					
				}
			});
			l("End editTeamName");
		}
	}, val);
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function acceptTeamInvite(id){
	l("Start acceptTeamInvite");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "group",
			mode : "acceptTeamInvite",
			ID: id
		},
		success : function(result) {
			l("acceptTeamInvite success");
			l(result);
			if (result.status !== true) {
				l("went smth wrong");
			} 
			loadTeamInvites();
			loadDuoTeams();
			loadDuoOpenTeams();
		}
	});
	l("End acceptTeamInvite");
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function declineTeamInvite(id){
	l("Start declineTeamInvite");
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "group",
			mode : "declineTeamInvite",
			ID: id
		},
		success : function(result) {
			l("declineTeamInvite success");
			l(result);
			if (result.status !== true) {
				l("smth went wrong");
			} 
			loadTeamInvites();
			loadDuoTeams();
			loadDuoOpenTeams();
		}
	});
	l("End declineTeamInvite");
}