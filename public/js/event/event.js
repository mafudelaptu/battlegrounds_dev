$(function() {
    // Handler for .ready() called.
    if (document.URL.indexOf("/event/") >= 0) {
        var created_event_id = getLastPartOfUrl();
        initChatNew("eventChat" + created_event_id, socket);
    }
});