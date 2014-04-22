$(function() {
    // Handler for .ready() called.
    l(getLastPartOfUrl());
    if ($(location).attr('pathname') == "/" || $(location).attr('pathname') == "/arena/" || $(location).attr('pathname') == "/battlegrounds/") {
        if (typeof socket != "undefined") {

            initChatNew("homeChat", socket);
            initSelectMatchmodeToggle();
        }
    }
});

function initSelectMatchmodeToggle() {
    var id = "#selectMatchmodesButton";
    $(id).once().click(function() {
        var content = "#selectMatchmodesContent";
        $(content).slideToggle("fast");
    });
}