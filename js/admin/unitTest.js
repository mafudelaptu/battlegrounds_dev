function testMatches() {
	$("#testResponse").html("");
	$.blockUI();
	$.ajax({
		url : '../ajaxAdmin.php',
		type : "POST",
		dataType : 'json',
		data : {
			type : "fakeMatches",
			mode : "unitTestMatches",
		},
		success : function(result) {
			l(result);
			$("#DEBUG_AREA").html(result.debug);
			
			html = result.test;
			ergebnis = result.ergebnis+" %";
			$("#testResponse").html("Status: true - "+ergebnis+html);
			$.unblockUI();
		}
	});
}