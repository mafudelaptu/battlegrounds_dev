/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
$(document).ready(
		function() {
			l(document.URL);
			if (document.URL.indexOf("/find_match.php") >= 0) {
				initChat("findMatch", $.datepicker.formatDate('yy/mm/dd', new Date()), "findMatchGeneralChat"); /* Start the inital request */
			}
			
			// 1vs1Queue Tab handling
			// zu 1vs1QueueTab springen wenn in url
			if(document.URL.indexOf("#1vs1Queue") > 0){
				$('#profileTabs a[href="#1vs1Queue"]').tab('show');
			}
			
		});


/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function getRegion(){
	ret = new Array();
	var region = $.cookie("region");
	ret.push(region);
	return ret;
}