/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function addmsgs(chatID, msgs) {
	l("add msgs");
	$.each(msgs, function(index, value) {
		$('#chat-' + chatID + '-area').append(value);
	});

	var container = $('#chat-' + chatID + '-area');

	var height = container.height();
	var scrollHeight = container[0].scrollHeight;
	var st = container.scrollTop();

	// if(st == 0 && scrolled == false){
	scrolled = true;
	var hovered = $("#chat-" + chatID + "-wrap").find(
			"#chat" + chatID + "-area:hover").length;

	if (hovered == 0) {
		setTimeout(function() {

			$("#chat-" + chatID + "-area").stop().animate({
				scrollTop : $("#chat-" + chatID + "-area")[0].scrollHeight
			}, 200);
		}, 10);
	}
	l("END add msgs");
}

/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function initChat(section, special, chatID) {
	// auf Enter abschicken
	// watch textarea for key presses
	$("#postComment-" + chatID).keydown(function(event) {

		var key = event.which;

		// all keys including return.
		if (key >= 33) {

			var maxLength = $(this).attr("maxlength");
			var length = this.value.length;

			// don't allow new content if length is maxed out
			if (length >= maxLength) {
				event.preventDefault();
			}
		}
	});
	// watch textarea for release of key press
	$("#postComment-" + chatID).keyup(function(e) {

		if (e.keyCode == 13) {
			l("press Enter");
			var text = $(this).val();
			var maxLength = $(this).attr("maxlength");
			var length = text.length;

			// send
			if (length <= maxLength + 1) {
				l("fast post");
				postMessage(section, special, chatID, text);
				l("nach post");
				$(this).val("");
			} else {
				$(this).val(text.substring(0, maxLength));
			}
		}
	});
	if(chatID != "findMatchChat"){
		chatLastTimestamp = 0;
		chatUpdate(section, special, chatID, chatLastTimestamp); /* Start the inital request */
	}
	l("initChat End");
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
var chatLastTimestamp = 0;
function chatUpdate(section, special, chatID, lastTimestamp) {
	l("updateChat");
	l(lastTimestamp);
	/*
	 * This requests the url "msgsrv.php" When it complete (or errors)
	 */
	if (typeof chatLastTimestamp == "undefined") {
		chatLastTimestamp = 0;
	}
	$.ajax({
		type : "POST",
		url : "updateChat.php",
		async : true, /*
						 * If set to non-async, browser shows page as
						 * "Loading.."
						 */
		dataType : 'json',
		data : {
			// type : "chat",
			// mode : "updateChat",
			section : section,
			special : special,
			lastTimestamp : chatLastTimestamp
		},
		cache : false,
		timeout : 20000, /* Timeout in ms */

		success : function(data) { /*
									 * called when request to barge.php
									 * completes
									 */
			l("success - new msg");
			l(data);

			if (typeof data != "undefined" && data != null) {
				if (data.status) {
					switch(chatID){
						case "findMatchGeneralChat":
							addmsgs(chatID, data.html); 
							addmsgs("findMatchChat", data.html); 
							break;
						default:
							addmsgs(chatID, data.html); 
					}
					
				} else {

				}
				l("lines Added!" + data.lastTimestamp);
				chatLastTimestamp = data.lastTimestamp;
			}

			setTimeout(function() {
				chatUpdate(section, special, chatID, chatLastTimestamp);
			}, 1000);

		},
		error : function(XMLHttpRequest, textStatus, errorThrown) {
			// addmsg("error", textStatus + " (" + errorThrown + ")");
			setTimeout(function() {
				l("error:" + errorThrown);
				updateChat(section, special, chatID, chatLastTimestamp); /*
																			 * Try
																			 * again
																			 * after..
																			 */
			}, 15000); /* milliseconds (15seconds) */
		}
	});
	l("END updateChat");
};
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function postMessage(section, special, chatID, msg) {
	l("postMessage Start");
	$.ajax({
		url : 'postMessage.php',
		type : "POST",
		dataType : 'json',
		data : {
//			type : "chat",
//			mode : "postComment",
			section : section,
			special : special,
			msg : msg
		},
		success : function(data) {
			l("postMessage success");
			chatUpdate(section, special, chatID, data.lastTimestamp);
			l(data);
		}
	});
	l("postMessage End");
}