/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function generateSingleQueueMatchMakingInfo(quickJoin, justCM) {
	div = $("#myModalMatchMaking .MatchMakingInfo");

	// auslesen was selektiert wurde
	//buttons = $("#SingleQueueMatchModes > button[class*='active']");
	
	
	html = "";
	//html += '<div class="row-fluid">' //+ '<div class="span6">'
	//+ '<strong>Selected regions</strong>' + '</div>'
	//+ '<div class="span6">';
	// Regionen und MatchModes auslesen und hinzufuegen
	var badges = new Array();
	if(quickJoin == true){
		var quali = $("#qualiHiddenIdentifier").val();
		var ret = new Array();
		if (typeof quali != "undefined") {
			l("justCM:"+justCM);
			html += '';
			if(justCM){
				badges.push("<span class='badge badge-info t' data-original-title='Captains-Mode' data-value='3'>CM</span>");
			}
			else{
				badges.push("<span class='badge badge-info t' data-original-title='Captains-Draft' data-value='9'>CD</span>");
			}
			
			l(badges);
		} else {
			html += '';
			badges.push("<span class='badge badge-info t' data-original-title='All-Pick' data-value='1'>AP</span>");
			badges.push("<span class='badge badge-info t' data-original-title='Single-Draft' data-value='2'>SD</span>");
			badges.push("<span class='badge badge-info t' data-original-title='Random-Draft' data-value='7'>RD</span>");
			badges.push("<span class='badge badge-info t' data-original-title='All-Random' data-value='8'>AR</span>");
			l(badges);
		}
		
	}
	else{
		badges =  $("#SingleQueueMatchModes .badge");
	}
	
	//html += '</div>'
	//+ '</div>';
	
	$.each(badges, function(index, value) {
		matchMode = $(value).attr("data-original-title");
		matchModeShortcut = $(value).html();
		matchModeID = $(value).attr("data-value");
		matchModeClass = $(value).attr("class");
//		html += '<div class="row-fluid">' + '<div class="span6">'
//				+ '<strong>'+matchMode+' ('+matchModeShortcut+')</strong>' + '</div>'
//				+ '<div class="span6" style="font-size:10px;">'
//				+ '	<span class="'+matchModeClass+'" id="labelPlayers'+matchModeID+'"  style="font-size:10px;">1/10</span> Players found <br>'
//				+ ' <span class="label" id="labelRangeU'+matchModeID+'"  style="font-size:10px;">0</span>-<span class="label" id="labelRangeO'+matchModeID+'"  style="font-size:10px;">0</span> Search range'
//				+ '<div id="maxSearchRangeReached'+matchModeID+'"></div>'
//				+ '</div>'
//				+ '</div>';
	
		html += '<div class="row-fluid">' + '<div class="span6">'
		+ '<strong>'+matchMode+' ('+matchModeShortcut+')</strong>' + '</div>'
		+ '<div class="span6" style="font-size:10px;">'
		+ '<div>Player(s) in Queue: <span class="'+matchModeClass+'" id="labelPlayers'+matchModeID+'"  style="font-size:10px;">1</span></div>'
		+ '<div class="positionInQueue">Your Position in Queue is: <span class="label" id="labelPosition'+matchModeID+'"  style="font-size:10px;">1</span></div>'
		//+ ' <span class="label" id="labelRangeU'+matchModeID+'"  style="font-size:10px;">0</span>-<span class="label" id="labelRangeO'+matchModeID+'"  style="font-size:10px;">0</span> Search range'
		//+ '<div id="maxSearchRangeReached'+matchModeID+'"></div>'
		+ '</div>'
		+ '</div>';
	});
	
	// html hinzufügen
	div.html(html);
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function updatePlayersFound(queue){
	l("Start updatePlayersFound");
	if(typeof queue !== undefined && queue != null){
		$.each(queue, function(index, value){
//			if(value >= 10){
//				$("#labelPlayers"+index).html("Match Ready! Please wait a moment!");
//			}
//			else{
//				$("#labelPlayers"+index).html(value+"/10");
//			}
			$("#labelPlayers"+index).html(value.count);
			
			// Queue position updaten
			$("#labelPosition"+index).html(value.position);
		});
	}
	
	l("End updatePlayersFound");
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function updateRange(range){
	l("Start updateRange");
	$.each(range, function(index, value){
		$("#labelRangeU"+index).html(value['untere_grenze']);
		$("#labelRangeO"+index).html(value['obere_grenze']);
		
		if(value['maxSearchRange']){
			$("#maxSearchRangeReached"+index).html("<span class='text-info'><small>max Search-Range reached</small></span>");
		}
		else{
			$("#maxSearchRangeReached"+index).html("");
		}
		
	});
	l("End updateRange");
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function handleMatchModeSelection(){
	l("Start handleMatchModeSelection");
	// Errors wieder löschen
	$("#SingleQueueMatchModesErrors").html("");
	
	// vorherige Selection löschen
	$("#SingleQueueMatchModes").html("");
	
	
	// angeklickte MatchMOdes auslesen
	var checkboxes = $("#myModalSelectMatchMode input[type='checkbox']");
	var checkedCheckboxes = $("#myModalSelectMatchMode input:checked").size();
	l(checkedCheckboxes);
	if(checkedCheckboxes > 0){
		$.each(checkboxes, function(index, checkbox){
			value = $(checkbox).val();
			name = $(checkbox).attr("name");
			shortcut = $(checkbox).attr("shortcut");
			
			$.cookie("singleQueue[" + value + "]", null);

			if($(checkbox).is(':checked')){
				l("checked");
				// Cookie setzen
				$.cookie("singleQueue[" + value + "]", true, {
					expires : 14
				});
				// Matchmodes zur auswahl hinzufügen
				selection = '<span class="badge badge-info t" data-value="'+value+'" data-original-title="'+name+'" title="'+name+'">'+shortcut+'</span>&nbsp;';
				$("#SingleQueueMatchModes").append(selection);
			}
		});
		
		$("#myModalSelectMatchMode").modal('hide');
		
	}
	else{
		var error = '<p class="text-error">Select at least one Matchmode!</p>';
		$("#myModalSelectMatchMode div.modal-body").append(error);
	}
	l("End handleMatchModeSelection");
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function handleRegionSelection(){
	l("Start handleRegionSelection");
	// Errors wieder löschen
	$("#SingleQueueRegionErrors").html("");
	
	// vorherige Selection löschen
	$("#SingleQueueRegion").html("");
	
	
	// angeklickte MatchMOdes auslesen
	var checkboxes = $("#myModalSelectRegion input[id*='region']");
	var checkedCheckboxes = $("#myModalSelectRegion input[id*='region']:checked").size();
	l(checkedCheckboxes);
	l(checkboxes);
	if(checkedCheckboxes > 0){
		$.each(checkboxes, function(index, checkbox){
			value = $(checkbox).val();
			name = $(checkbox).attr("name");
			shortcut = $(checkbox).attr("shortcut");
			
			$.cookie("singleQueueRegion[" + value + "]", null);

			if($(checkbox).is(':checked')){
				l("checked");
				// Cookie setzen
				$.cookie("singleQueueRegion[" + value + "]", true, {
					expires : 14
				});
				
				// Regions zur auswahl hinzufügen
				selection = '<span class="badge badge-important t" data-value="'+value+'" data-original-title="'+name+'" title="'+name+'">'+shortcut+'</span>&nbsp;';
				$("#SingleQueueRegion").append(selection);
			}
		});
		
		$("#myModalSelectRegion").modal('hide');
		
	}
	else{
		var error = '<p class="text-error">Select at least one Region!</p>';
		$("#myModalSelectRegion div.modal-body").append(error);
	}
	l("End handleRegionSelection");
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function updateNextMatchmakingTime(time){
	$("#nextMatchmaking").html(time);
}

function updateUserPool(skillBracket){
	l("updateUserPool");
	var elem = $("#userPoolSpan");
	l(skillBracket);
	$(elem).html(skillBracket);
	
	l("End updateUserPool");
}

function updateQueueStats(queueCounts){
	l("updateQueueStats");
	l(queueCounts);
	$("#prisonQueueCount").html(queueCounts[1]);
	$("#traineeQueueCount").html(queueCounts[2]);
	$("#amateurOrHigherQueueCount").html(queueCounts[3]);
	$("#forceQueueCount").html(queueCounts[-1]);
	l("End updateQueueStats");
}
