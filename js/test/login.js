/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function loginAsFakeUser(steamID){
	l("Start loginFakeUser");
	l(steamID);
	$.ajax({
		url : 'ajax.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "test",
			mode : "handleFakeLogin",
			ID: steamID
		},
		success : function(result) {
			l("success loginFakeUser");
			l(result);
			window.location = result.url;
		}
	});
	l("End loginFakeUser");
}