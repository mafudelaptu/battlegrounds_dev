$(function() {
    // Handler for .ready() called.
    if (document.URL.indexOf("/profile/") >= 0) {
        //init Graphs
        initProfileGraphs();
        initButtons();
    }
});

function initProfileGraphs() {
    if ($("#generalWinRateTrendChart").length > 0) {
        //drawGeneralWinRateTrendChart();
    }

    // ELO-Rose
    if ($("#pointRoseChart").length > 0) {
        drawPointRoseChart();
    }

    // Elo-History
    if ($("#pointHistoryChart").length > 0) {
        drawPointHistoryChart();
    }
}

function initButtons() {

    $(".switchMatchtype").click(function() {
        var that = $(this);
        if (!that.hasClass("alert-info")) {
            //button visualisation
            $(".switchMatchtype").removeClass("alert-info");
            $(".switchMatchtype").addClass("btn-link");
            that.removeClass("btn-link");
            that.addClass("alert-info");

            // switch stats
            var matchtype_id = that.attr("data-id");
            l(matchtype_id);

            // points & ranking
            $(".profile_userstats").addClass("hide");
            $("#userstats_" + matchtype_id).removeClass("hide");

            //table wins etc
            $(".table_userstats").addClass("hide");
            $("#table_userstats_" + matchtype_id).removeClass("hide");

            $(".lvlup_user").addClass("hide");
            $("#lvlup_user_" + matchtype_id).removeClass("hide");
        }
    });

    $("#syncWithSteam").click(function() {
        $.blockUI({
            message: "<h4>fetching Steam data...</h4>",
            css: {
                color: "#000"
            }
        });

        $.ajax({
            url: ARENA_PATH + "/profile/syncWithSteam",
            type: "POST",
            dataType: 'json',
            success: function(result) {
                $.unblockUI();
                if (result.status == true) {
                    window.location = ARENA_PATH + "/profile";
                } else {
                    var title = "Steam-sync failed";
                    var msg = "Steam didnt respond fast enough. Your syncing failed.";
                    bootbox.dialog({
                        message: msg,
                        title: title,
                        buttons: {
                            again: {
                                label: "try again",
                                className: "btn-default",
                                callback: function() {
                                    $("#syncWithSteam").click();
                                }
                            },
                            cancel: {
                                label: "cancel",
                                className: "btn-default",
                                callback: function() {

                                }
                            }
                        }
                    });
                }
            }
        });
    });
}