$(document).ready(
		function() {
			l(document.URL);
			if (document.URL.indexOf("/editMatchResult.php") >= 0) {
				
			}
		});


function mREditPointsChanges(elem){
	l("Start mREditPointsChanges");
	
	var steamID = $(elem).attr("data-value");
	var matchID = getParameterByName("matchID");
	
	l(steamID+" "+matchID);
	
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "UserPoints",
			mode : "getUserPointsData",
			steamID : steamID,
			matchID : matchID
		},
		success : function(result) {
			l("mREditPointsChanges success");
			l(result);
			if (result.status !== true) {
				
			}
			else {
				$("#mRSteamIDIdentifyer").html(steamID);
				$("#mREditUserPointsContent").html(result.html);
				$("#editUserPointsModal").modal("show");
			}
		}
	});
	l("End mREditPointsChanges");
}

function addUserPointFormsForModal(){
	var content =  $("#mREditUserPointsContent");
	l(content);
	
	var count = content.find("input").size();
	count = count + 1;
	
	html = '<div class="row-fluid" id="mRRow'+count+'">';
	
	// input bauen
	html += '<div class="span2"><input class="input-mini" type="text" value="" name="mRPC'+count+'" id="mRPC'+count+'"/></div>';
	
	// select bauen
	if(count == 1){
		l("Start fetchPointsTypeSelectTPL");
		$.ajax({
			url : '../ajaxAdmin.php',
			type : "POST",
			dataType : 'json',
			data : {
				type : "SmartyFetch",
				mode : "fetchPointsTypeSelectTPL"
			},
			success : function(result) {
				l("fetchPointsTypeSelectTPL success");
				var select = $("#mRRow1 select[name=mRPT1]").html();
				html += '<div class="span8">'+result.data+'</div>';
				// delete button bauen
				html += '<div class="span2"><button type="button" class="btn btn-danger" onclick="mRRemoveUserPointsInputs('+count+')"><i class="icon-remove-sign"></i></button></div>';
				
				html += '</div>';
				l("###############");
				l(html);
				content.append(html);
			}
		});
		l("End fetchPointsTypeSelectTPL");
	}
	else{
		var select = $("#mRRow1 select[name=mRPT1]").html();
		html += '<div class="span8"><select name="mRPT'+count+'">'+select+'</select></div>';
		// delete button bauen
		html += '<div class="span2"><button type="button" class="btn btn-danger" onclick="mRRemoveUserPointsInputs('+count+')"><i class="icon-remove-sign"></i></button></div>';
		
		html += '</div>';
		l("###############");
		l(html);
		content.append(html);
	}

}

function mRRemoveUserPointsInputs(rowID){
	var content =  $("#mREditUserPointsContent");
	var count = content.find("input").size();
	//if(count > 1){
		$('#mRRow'+rowID).remove();
	//}
	//else{
	//	alert("You cant remove the first item. If you want to remove all Points use the remove button on team-view.");
	//}
}

function mRSaveUserPointsChanges(){
	var steamID = $("#mRSteamIDIdentifyer").html();
	var matchID = getParameterByName("matchID");
	
	// inputs auslesen
	var inputs = $("#mREditUserPointsContent").find("input");
	l(inputs);
	jsonArr = new Array();
	$.each(inputs, function(index, value) {
		var val = $(value).val();
		
		// select-wert auslesen
		var select = $("#mREditUserPointsContent").find("select")[index];
		var pointsTypeID = $(select).find("option:selected").val();
		
		// json zusammenbauen
		var obj = {"value":val,"pointsTypeID":pointsTypeID};
		jsonArr.push(obj);
	});
	l(jsonArr);
	l("Start mRSaveUserPointsChanges");
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "UserPoints",
			mode : "saveUserPointChanges",
			steamID : steamID,
			matchID : matchID,
			data: jsonArr
		},
		success : function(result) {
			l("mRSaveUserPointsChanges success");
			l(result);
			window.location.reload();
		}
	});
	l("End mRSaveUserPointsChanges");
}

function mRdeleteAllUserPoints(elem){
	var steamID = $(elem).attr("data-value");
	var matchID = getParameterByName("matchID");
	
	l("Start loadDuoTeams");
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "UserPoints",
			mode : "deleteAllUserPoints",
			steamID : steamID,
			matchID: matchID
		},
		success : function(result) {
			l("loadDuoTeams success");
			l(result);

				window.location.reload();
			
		}
	});
	l("End loadDuoTeams");
}

function mRMarkUserAsLeaver(elem){
	var steamID = $(elem).attr("data-value");
	var matchID = getParameterByName("matchID");

	l("Start mRMarkUserAsLeaver");
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "MatchDetailsLeaverVotes",
			mode : "markUserAsLeaver",
			steamID : steamID,
			matchID: matchID
		},
		success : function(result) {
			l("mRMarkUserAsLeaver success");
			l(result);
			window.location.reload();
		}
	});
	l("End mRMarkUserAsLeaver");
	
}

function mRDemarkAsLeaver(elem){
	var steamID = $(elem).attr("data-value");
	var matchID = getParameterByName("matchID");
	l("Start mRDemarkAsLeaver");
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "MatchDetailsLeaverVotes",
			mode : "demarkUserAsLeaver",
			steamID : steamID,
			matchID: matchID
		},
		success : function(result) {
			l("mRDemarkAsLeaver success");
			l(result);
			window.location.reload();
		}
	});
	l("End mRDemarkAsLeaver");
	
}