


function getSelected3vs3QueueModi() {
	ret = new Array();
	// buttons = $("#singleQueueArea div[data-toggle='buttons-checkbox'] >
	// button");
	badges = $("#3vs3QueueMatchModes .badge");
	l(badges);
	$.each(badges, function(index, value) {
		var val = $(value).attr("data-value");
		// $.cookie("singleQueue[" + $(value).val() + "]", null); // alle
		// Spielmodi
		// Cookies
		// löschen
		// if ($(value).hasClass("active")) {
		// ret.push($(value).val());
		// }
		ret.push(val);
	});
	l(ret);
	return ret;
}

function getSelected3vs3QueueRegions() {
	ret = new Array();
	badges = $("#3vs3QueueRegion .badge");
	l(badges);
	$.each(badges, function(index, value) {
		var val = $(value).attr("data-value");
		// $.cookie("singleQueue[" + $(value).val() + "]", null); // alle
		// Spielmodi
		// Cookies
		// löschen
		// if ($(value).hasClass("active")) {
		// ret.push($(value).val());
		// }
		ret.push(val);
	});
	l(ret);
	return ret;
}

function getQuickJoin3vs3QueueModi(){
	
	ret = new Array();
	ret.push("1"); // AP
	ret.push("2"); // SD
	ret.push("7"); // RD
	ret.push("8"); // AR
	l(ret);
	return ret;
}

function getQuickJoin3vs3QueueRegions(){
	ret = new Array();
	ret.push("1"); // AUTO
	ret.push("2"); // AUTO
	ret.push("3"); // AUTO
	ret.push("4"); // AUTO
	ret.push("5"); // AUTO
	ret.push("6"); // AUTO
	ret.push("7"); // AUTO
	ret.push("8"); // AUTO
	ret.push("9"); // AUTO
	l(ret);
	return ret;
}

function checkIfUserAlreadyHave3vs3Stats(){
	l("checkIfUserAlreadyHave3vs3Stats Start");
	matchTypeID = 9;
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "userElo",
			mode : "checkIfUserHaveEloForMatchType",
			ID: matchTypeID
		},
		success : function(result) {
			l("checkIfUserAlreadyHave3vs3Stats success");
			l(result);
			if (!result.status) {
				// f�r 3vs3Queue gibts noch keine Elo Werte
				l("insert3vs3EloValues Start");
				$.ajax({
					url : 'ajax.php',
					type : "POST",
					dataType : 'json',
					data : {
						type : "userElo",
						mode : "insertFirstEloForMatchType",
						ID: matchTypeID
					},
					success : function(result) {
						l("insert3vs3EloValues success");
						l(result);
						if (!result.status) {
							l(result);
						}

					}
				});
				l("insert3vs3EloValues End");
			}

		}
	});
	l("checkIfUserAlreadyHave3vs3Stats End");
}


