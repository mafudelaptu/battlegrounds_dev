
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function logout(){
	l("Start logout");
	$.ajax({
		url: 'ajax.php',
		type: "POST",
		dataType: 'json',
		data: {type:"login", mode: "handleLogout" },
		success: function(result) {
			console.log("logout success");
			l(result);
			if(result.status !== true){
				if(debug){
					$("#notification > p").html(result.status);
					$("#notification").slideDown();
				}
			}
			else{
				// Umleitung aufs hauptseite
				if(result.url != ""){
					window.location = result.url;
				}
			}
		}
	});
	l("End logout");
}