$(function() {
  // Handler for .ready() called.
  if (document.URL.indexOf("/event/") >= 0) {
  	var created_event_id = getLastPartOfUrl();
  		initChat("eventChat"+created_event_id);
  }
});