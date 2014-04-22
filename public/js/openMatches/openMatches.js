$(function() {
    // Handler for .ready() called.
    if (document.URL.indexOf("/openMatches") >= 0) {
        initOpenMatchesTable();
    }
});

function initOpenMatchesTable() {
    l("init table");
    $("#openMatchesTable").dataTable({
        "sPaginationType": "bootstrap",
        "aaSorting": [
            [3, "desc"]
        ],
        "aoColumns": [{
            "bSortable": false
        }, {
            "bSortable": false
        }, {
            "bSortable": false
        }, {
            "sType": "numeric-html"
        }, {
            "bSortable": false
        }, {
            "bSortable": false
        }]
    });

}