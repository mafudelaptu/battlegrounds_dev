/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function setLeaverCounts(data){
	l("Start setLeaverCounts");
	l(data);
	$.each(data, function(steamID, count){
		countHTML = "";
		if(count > 0){
			
			var votesLabel = (count == 1 ? "Vote" : "Votes");
			
			countHTML +=  '<span class="text-error">';
			countHTML += count+' '+votesLabel;
			countHTML +=	'</span>';
			
			id = "#label_"+steamID;
			
			// vorherigen Vote löschen
			$(id+" .text-error").remove();
			
			label = $(id);

			label.append(countHTML);
		}
		
	});
	l("End setLeaverCounts");
}