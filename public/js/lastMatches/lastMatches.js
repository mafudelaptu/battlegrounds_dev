$(function() {
    // Handler for .ready() called.
    if (document.URL.indexOf("/lastMatches") >= 0) {
        initLastMatchesTable();
    }
});

function initLastMatchesTable() {
    l("init table");
    $("#lastMatchesTable").dataTable({
        "sPaginationType": "bootstrap",
        "aaSorting": [
            [2, "desc"]
        ],
        "aoColumns": [{
            "bSortable": false
        }, {
            "bSortable": false
        }, {
            "sType": "numeric-html"
        }, {
            "bSortable": false
        }]
    });

}