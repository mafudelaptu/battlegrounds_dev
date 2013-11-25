/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
$(document).ready(function() {
	if ($("#profileTabs").length > 0) {
		
		// zu teams springen wenn in url
		if(document.URL.indexOf("#warns") > 0){
			$('#profileTabs a[href="#warns"]').tab('show');
		}
		
		$("#warnHistoryTable").dataTable({
			"destroy":true,
			"aaSorting": [[ 0, "desc" ]],
			
		});
	}
});