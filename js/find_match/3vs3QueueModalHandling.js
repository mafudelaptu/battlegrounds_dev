function generate3vs3QueueMatchMakingInfo(quickJoin) {
	div = $("#myModalMatchMaking .MatchMakingInfo");

	// auslesen was selektiert wurde
	//buttons = $("#SingleQueueMatchModes > button[class*='active']");
	
	
	html = "";
	html += '<div class="row-fluid">' + '<div class="span6">'
	+ '<strong>Selected regions</strong>' + '</div>'
	+ '<div class="span6">';
	// Regionen und MatchModes auslesen und hinzufuegen
	var badges = new Array();
	if(quickJoin == true){
		html += '<span class="badge badge-important t" data-original-title="Automatic">AUTO</span>';
		badges.push("<span class='badge badge-info t' data-original-title='All-Pick' data-value='1'>AP</span>");
		badges.push("<span class='badge badge-info t' data-original-title='Single-Draft' data-value='2'>SD</span>");
		badges.push("<span class='badge badge-info t' data-original-title='Random-Draft' data-value='7'>RD</span>");
		badges.push("<span class='badge badge-info t' data-original-title='All-Random' data-value='8'>AR</span>");
		l(badges);
	}
	else{
		regions = $("#3vs3QueueRegion .badge");
		$.each(regions, function(index, value) {
			html += '<span class="'+$(value).attr("class")+'">'+$(value).html()+'</span>&nbsp;';
		});
		l(regions);
		badges =  $("#3vs3QueueMatchModes .badge");
	}
	
	html += '</div>'
	+ '</div>';
	
	$.each(badges, function(index, value) {
		matchMode = $(value).attr("data-original-title");
		matchModeShortcut = $(value).html();
		matchModeID = $(value).attr("data-value");
		matchModeClass = $(value).attr("class");
		html += '<div class="row-fluid">' + '<div class="span6">'
				+ '<strong>'+matchMode+' ('+matchModeShortcut+')</strong>' + '</div>'
				+ '<div class="span6" style="font-size:10px;">'
				+ '	<span class="'+matchModeClass+'" id="labelPlayers'+matchModeID+'"  style="font-size:10px;">1/10</span> Players found <br>'
				+ ' <span class="label" id="labelRangeU'+matchModeID+'"  style="font-size:10px;">0</span>-<span class="label" id="labelRangeO'+matchModeID+'"  style="font-size:10px;">0</span> Search range'
				+ '<div id="maxSearchRangeReached'+matchModeID+'"></div>'
				+ '</div>'
				+ '</div>';
	});
	
	// html hinzufügen
	div.html(html);
}

function update3vs3QueuePlayersFound(queue){
	l("Start updatePlayersFound");
	if(typeof queue !== undefined && queue != null){
		$.each(queue, function(index, value){
			if(value >= 6){
				$("#labelPlayers"+index).html("Match Ready! Please wait a moment!");
			}
			else{
				$("#labelPlayers"+index).html(value+"/10");
			}
			
		});
	}
	
	l("End updatePlayersFound");
}


function handleMatchMode3vs3Selection(){
	l("Start handleMatchMode3vs3Selection");
	// Errors wieder löschen
	$("#3vs3QueueMatchModesErrors").html("");
	
	// vorherige Selection löschen
	$("#3vs3QueueMatchModes").html("");
	
	
	// angeklickte MatchMOdes auslesen
	var checkboxes = $("#myModalSelectMatchMode3vs3 input[type='checkbox']");
	var checkedCheckboxes = $("#myModalSelectMatchMode3vs3 input:checked").size();
	l(checkedCheckboxes);
	if(checkedCheckboxes > 0){
		$.each(checkboxes, function(index, checkbox){
			value = $(checkbox).val();
			name = $(checkbox).attr("name");
			shortcut = $(checkbox).attr("shortcut");
			
			$.cookie("3vs3Queue[" + value + "]", null);

			if($(checkbox).is(':checked')){
				l("checked");
				// Cookie setzen
				$.cookie("3vs3Queue[" + value + "]", true, {
					expires : 14
				});
				// Matchmodes zur auswahl hinzufügen
				selection = '<span class="badge badge-info t" data-value="'+value+'" data-original-title="'+name+'" title="'+name+'">'+shortcut+'</span>&nbsp;';
				$("#3vs3QueueMatchModes").append(selection);
			}
		});
		
		$("#myModalSelectMatchMode3vs3").modal('hide');
		
	}
	else{
		var error = '<p class="text-error">Select at least one Matchmode!</p>';
		$("#myModalSelectMatchMode3vs3 div.modal-body").append(error);
	}
	l("End handleMatchMode3vs3Selection");
}

function handleRegion3vs3Selection(){
	l("Start handleRegion3vs3Selection");
	// Errors wieder löschen
	$("#3vs3QueueRegionErrors").html("");
	
	// vorherige Selection löschen
	$("#3vs3QueueRegion").html("");
	
	
	// angeklickte MatchMOdes auslesen
	var checkboxes = $("#myModalSelectRegion3vs3 input[id*='region']");
	var checkedCheckboxes = $("#myModalSelectRegion3vs3 input[id*='region']:checked").size();
	l(checkedCheckboxes);
	l(checkboxes);
	if(checkedCheckboxes > 0){
		$.each(checkboxes, function(index, checkbox){
			value = $(checkbox).val();
			name = $(checkbox).attr("name");
			shortcut = $(checkbox).attr("shortcut");
			
			$.cookie("3vs3QueueRegion[" + value + "]", null);

			if($(checkbox).is(':checked')){
				l("checked");
				// Cookie setzen
				$.cookie("3vs3QueueRegion[" + value + "]", true, {
					expires : 14
				});
				
				// Regions zur auswahl hinzufügen
				selection = '<span class="badge badge-important t" data-value="'+value+'" data-original-title="'+name+'" title="'+name+'">'+shortcut+'</span>&nbsp;';
				$("#3vs3QueueRegion").append(selection);
			}
		});
		
		$("#myModalSelectRegion3vs3").modal('hide');
		
	}
	else{
		var error = '<p class="text-error">Select at least one Region!</p>';
		$("#myModalSelectRegion3vs3 div.modal-body").append(error);
	}
	l("End handleRegion3vs3Selection");
}