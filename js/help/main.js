/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
$(document).ready(function() {
  
	if(document.URL.indexOf("/help.php")>=0){
		
		// Wenn geladen, dann inhaltsverzeichnis erstellen
		// Elemente auslesen
		var elems = $("#faqContent a[data-target]");
		html = "<ol>";
		var i = 1;
		$.each(elems, function(index, value){
			// Überschrift
			header = $(value).html();
			headerHref = $(value).attr("data-target");
			html += '<li><a href="help.php'+headerHref+'">'+header+'</a></li>';
			
			$(value).html(i+". "+header);
			i++;
			
		});
		html += "<ol>";
		
		// zum inhaltsverzeichnis DIV hinzufügen
		$("#faqInhaltsverzeichnis").html(html);
		
	}
	
});