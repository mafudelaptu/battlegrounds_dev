

/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */


function getSelected1vs1QueueModi() {
	ret = new Array();
	// buttons = $("#singleQueueArea div[data-toggle='buttons-checkbox'] >
	// button");
//	badges = $("#1vs1QueueMatchModes .badge");
//	l(badges);
//	$.each(badges, function(index, value) {
//		var val = $(value).attr("data-value");
//		// $.cookie("singleQueue[" + $(value).val() + "]", null); // alle
//		// Spielmodi
//		// Cookies
//		// löschen
//		// if ($(value).hasClass("active")) {
//		// ret.push($(value).val());
//		// }
//		ret.push(val);
//	});
	ret.push(5);
	l(ret);
	return ret;
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function getSelected1vs1QueueRegions() {
	ret = new Array();
	badges = $("#1vs1QueueRegion .badge");
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
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function getQuickJoin1vs1QueueModi(){
	
	ret = new Array();
	ret.push("1"); // AP
	ret.push("2"); // SD
	ret.push("7"); // RD
	ret.push("8"); // AR
	l(ret);
	return ret;
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function getQuickJoin1vs1QueueRegions(){
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
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function checkIfUserAlreadyHave1vs1Stats(){
	l("checkIfUserAlreadyHave1vs1Stats Start");
	matchTypeID = 8;
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
			l("checkIfUserAlreadyHave1vs1Stats success");
			l(result);
			if (!result.status) {
				// f�r 1vs1Queue gibts noch keine Elo Werte
				l("insert1vs1EloValues Start");
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
						l("insert1vs1EloValues success");
						l(result);
						if (!result.status) {
							l(result);
						}

					}
				});
				l("insert1vs1EloValues End");
			}

		}
	});
	l("checkIfUserAlreadyHave1vs1Stats End");
}


