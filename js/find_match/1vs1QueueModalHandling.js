/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function generate1vs1QueueMatchMakingInfo() {
	div = $("#myModalMatchMaking .MatchMakingInfo");

	// auslesen was selektiert wurde
	//buttons = $("#SingleQueueMatchModes > button[class*='active']");
	
	
	html = "";
	//html += '<div class="row-fluid">' + '<div class="span6">'
	//+ '<strong>Selected regions</strong>' + '</div>'
	//+ '<div class="span6">';
	// Regionen und MatchModes auslesen und hinzufuegen
	var badges = new Array();
//	if(quickJoin == true){
//		html += '<span class="badge badge-important t" data-original-title="Automatic">AUTO</span>';
//		badges.push("<span class='badge badge-info t' data-original-title='All-Pick' data-value='1'>AP</span>");
//		badges.push("<span class='badge badge-info t' data-original-title='Single-Draft' data-value='2'>SD</span>");
//		badges.push("<span class='badge badge-info t' data-original-title='Random-Draft' data-value='7'>RD</span>");
//		badges.push("<span class='badge badge-info t' data-original-title='All-Random' data-value='8'>AR</span>");
//		l(badges);
//	}
//	else{
//		regions = $("#1vs1QueueRegion .badge");
//		$.each(regions, function(index, value) {
//			html += '<span class="'+$(value).attr("class")+'">'+$(value).html()+'</span>&nbsp;';
//		});
//		l(regions);
		//badges =  $("#1vs1QueueMatchModes .badge");
	//}
	
	//html += '</div>';
	
//	$.each(badges, function(index, value) {
//		matchMode = $(value).attr("data-original-title");
//		matchModeShortcut = $(value).html();
//		matchModeID = $(value).attr("data-value");
//		matchModeClass = $(value).attr("class");
//		html += '<div class="row-fluid">' + '<div class="span6">'
//				+ '<strong>'+matchMode+' ('+matchModeShortcut+')</strong>' + '</div>'
//				+ '<div class="span6" style="font-size:10px;">'
//				+ '	<span class="'+matchModeClass+'" id="labelPlayers'+matchModeID+'"  style="font-size:10px;">1/10</span> Players found <br>'
//				+ ' <span class="label" id="labelRangeU'+matchModeID+'"  style="font-size:10px;">0</span>-<span class="label" id="labelRangeO'+matchModeID+'"  style="font-size:10px;">0</span> Search range'
//				+ '<div id="maxSearchRangeReached'+matchModeID+'"></div>'
//				+ '</div>'
//				+ '</div>';
//	});
	
	matchMode = "Construct";
	matchModeShortcut = "CONST";
	matchModeID = "5";
	matchModeClass = "badge badge-info t";
	html += '<div class="row-fluid">' + '<div class="span6">'
			+ '<strong>'+matchMode+' ('+matchModeShortcut+')</strong>' + '</div>'
			+ '<div class="span6" style="font-size:10px;">'
			+ '<div>Player(s) in Queue: <span class="'+matchModeClass+'" id="labelPlayers'+matchModeID+'"  style="font-size:10px;">1</span></div>'
			+ '<div class="positionInQueue">Your Position in Queue is: <span class="label" id="labelPosition'+matchModeID+'"  style="font-size:10px;">1</span></div>'
			//+ ' <span class="label" id="labelRangeU'+matchModeID+'"  style="font-size:10px;">0</span>-<span class="label" id="labelRangeO'+matchModeID+'"  style="font-size:10px;">0</span> Search range'
			+ '<div id="maxSearchRangeReached'+matchModeID+'"></div>'
			+ '</div>'
			+ '</div>';
	// html hinzufügen
	div.html(html);
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function update1vs1QueuePlayersFound(queue){
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
function handleMatchMode1vs1Selection(){
	l("Start handleMatchMode1vs1Selection");
	// Errors wieder löschen
	$("#1vs1QueueMatchModesErrors").html("");
	
	// vorherige Selection löschen
	$("#1vs1QueueMatchModes").html("");
	
	
	// angeklickte MatchMOdes auslesen
	var checkboxes = $("#myModalSelectMatchMode1vs1 input[type='checkbox']");
	var checkedCheckboxes = $("#myModalSelectMatchMode1vs1 input:checked").size();
	l(checkedCheckboxes);
	if(checkedCheckboxes > 0){
		$.each(checkboxes, function(index, checkbox){
			value = $(checkbox).val();
			name = $(checkbox).attr("name");
			shortcut = $(checkbox).attr("shortcut");
			
			$.cookie("1vs1Queue[" + value + "]", null);

			if($(checkbox).is(':checked')){
				l("checked");
				// Cookie setzen
				$.cookie("1vs1Queue[" + value + "]", true, {
					expires : 14
				});
				// Matchmodes zur auswahl hinzufügen
				selection = '<span class="badge badge-info t" data-value="'+value+'" data-original-title="'+name+'" title="'+name+'">'+shortcut+'</span>&nbsp;';
				$("#1vs1QueueMatchModes").append(selection);
			}
		});
		
		$("#myModalSelectMatchMode1vs1").modal('hide');
		
	}
	else{
		var error = '<p class="text-error">Select at least one Matchmode!</p>';
		$("#myModalSelectMatchMode1vs1 div.modal-body").append(error);
	}
	l("End handleMatchMode1vs1Selection");
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function handleRegion1vs1Selection(){
	l("Start handleRegion1vs1Selection");
	// Errors wieder löschen
	$("#1vs1QueueRegionErrors").html("");
	
	// vorherige Selection löschen
	$("#1vs1QueueRegion").html("");
	
	
	// angeklickte MatchMOdes auslesen
	var checkboxes = $("#myModalSelectRegion1vs1 input[id*='region']");
	var checkedCheckboxes = $("#myModalSelectRegion1vs1 input[id*='region']:checked").size();
	l(checkedCheckboxes);
	l(checkboxes);
	if(checkedCheckboxes > 0){
		$.each(checkboxes, function(index, checkbox){
			value = $(checkbox).val();
			name = $(checkbox).attr("name");
			shortcut = $(checkbox).attr("shortcut");
			
			$.cookie("1vs1QueueRegion[" + value + "]", null);

			if($(checkbox).is(':checked')){
				l("checked");
				// Cookie setzen
				$.cookie("1vs1QueueRegion[" + value + "]", true, {
					expires : 14
				});
				
				// Regions zur auswahl hinzufügen
				selection = '<span class="badge badge-important t" data-value="'+value+'" data-original-title="'+name+'" title="'+name+'">'+shortcut+'</span>&nbsp;';
				$("#1vs1QueueRegion").append(selection);
			}
		});
		
		$("#myModalSelectRegion1vs1").modal('hide');
		
	}
	else{
		var error = '<p class="text-error">Select at least one Region!</p>';
		$("#myModalSelectRegion1vs1 div.modal-body").append(error);
	}
	l("End handleRegion1vs1Selection");
}