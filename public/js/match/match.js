$(function() {
    // Handler for .ready() called.
    if (document.URL.indexOf("/match/") >= 0) {
        var match_id = getLastPartOfUrl();
        // init Buttons
        initMatchButtons();
        if (typeof socket != "undefined") {
            initChatNew("matchChat" + match_id, socket);
        }
    }
});

function initMatchButtons() {
    // Submit Button
    $("#matchSubmitButton").click(function() {
        var match_id = getLastPartOfUrl();
        $.ajax({
            url: ARENA_PATH + "/match/getSubmitModal",
            type: "GET",
            dataType: 'json',
            data: {
                fake: false,
                match_id: match_id
            },
            success: function(html_data) {
                l(html_data);
                $("#generalModal div.modal-content").html(html_data.html);

                initScreenshotupload(match_id);

                $("#generalModal").modal("show");

                $("#submitMatchResultButton").click(function() {
                    l(html_data.matchtype_id);
                    submitMatchResult(match_id, html_data.matchtype_id);
                });

            }
        });
    });

    $(".votebutton").click(function() {
        matchVotePlayer(this);
    });

    $("#matchCancelButton").click(function() {
        var match_id = getLastPartOfUrl();
        $.ajax({
            url: ARENA_PATH + "/match/getCancelModal",
            type: "GET",
            dataType: 'json',
            data: {
                fake: false,
                match_id: match_id
            },
            success: function(html_data) {
                l(html_data);
                $("#generalModal div.modal-content").html(html_data.html);
                $("#generalModal").modal("show");

                $("#submitCancelButton").click(function() {
                    submitCancelMatch(match_id);
                });

            }
        });
    });

}

function initScreenshotupload(match_id) {
    $("#screenshotUpload").fileupload({
        dataType: "json",
        done: function(e, data) {
            $("#ScreenshotProgress").hide();
            l(data);


            var fileUploaded = data.files[0];

            $.ajax({
                url: ARENA_PATH + '/match/moveScreenshotFile',
                type: "POST",
                dataType: 'json',
                data: {
                    fake: false,
                    match_id: match_id,
                    fileName: fileUploaded.name
                },
                success: function(result) {
                    var filename = result.fileName;
                    $.each(data.files, function(index, file) {
                        var html = '<img src="' + ARENA_PATH + '/files/screenshots/' + match_id + '/' + filename + '" class="t" title="' + file.name + '" alt="' + file.name + '" width="150"><br>' + file.name;
                        $("#screenshotUploaded").html(html);
                    });
                    l(result);
                }
            });
        },
        add: function(e, data) {
            var file = data.files[0];
            l(file);
            removeScreenshotError();
            if (file.type.indexOf("image/") === 0) {
                $('#ScreenshotProgress .progress-bar').css(
                    'width', '0%'
                );
                data.submit();
            } else {
                alert("wrong documenttype for upload. Just image-files allowed!");
            }
        },
        progressall: function(e, data) {
            $("#ScreenshotProgress").removeClass("hide").show();
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#ScreenshotProgress .progress-bar').css(
                'width',
                progress + '%'
            );
        }
    });
}

function submitMatchResult(match_id, matchtype_id) {
    l(matchtype_id);
    var radio = $("#checkWinLose .active > input").val();
    var valid = validateMatchResult(radio);
    var leaverArray = getLeaverArray();
    var validScreenshot = true;

    if (matchtype_id == "2") {
        validScreenshot = checkScreenshotUploaded();
    }

    if (valid) {
        matchResultValidError("hide");
        if (validScreenshot) {
            matchResultValidError("hide");
            $.ajax({
                url: ARENA_PATH + "/match/submitResult",
                type: "POST",
                dataType: 'json',
                data: {
                    fake: false,
                    match_id: match_id,
                    result: radio,
                    leaver: leaverArray
                },
                success: function(response) {
                    l(response);
                    switch (response.status) {
                        case "screenshotNotUploaded":
                            showScreenshotError();
                            break;
                        default:
                            $("#generalModal").modal("hide");

                            showSuccessMatchResultSubmission(response.isEventMatch);
                            break;
                    }


                }
            });
        } else {
            showScreenshotError();
        }

    } else {
        matchResultValidError("show");
    }

}

function validateMatchResult(radio) {
    var ret = false;
    // check winLose
    l(radio);
    if (radio != "" && typeof radio != "undefined") {
        l("true");
        ret = true;
    }

    return ret;
}

function matchResultValidError(type) {
    l(type);
    switch (type) {
        case "show":
            $("#checkWinLoseErrorDiv").removeClass("hide");
            break;
        case "hide":
            $("#checkWinLoseErrorDiv").addClass("hide");

            break;
    }
}

function showSuccessMatchResultSubmission(isEventMatch) {
    var msg = "You successfully submitted the matchresult! Do you want to stay on the matchpage?";
    var title = "Match result";
    if (isEventMatch.status == true) {
        bootbox.dialog({
            message: msg,
            title: title,
            buttons: {
                stay: {
                    label: "Stay on matchpage!",
                    className: "btn-default",
                    callback: function() {
                        window.location.reload();
                    }
                },
                eventPage: {
                    label: "Go back to Event-Page",
                    className: "btn-success",
                    callback: function() {
                        window.location.href = ARENA_PATH + "/event/" + isEventMatch.event_id + "/" + isEventMatch.created_event_id;
                    }
                },
            }
        });
    } else {
        bootbox.dialog({
            message: msg,
            title: title,
            buttons: {
                success: {
                    label: "Stay on matchpage!",
                    className: "btn-default",
                    callback: function() {
                        window.location.reload();
                    }
                },
                danger: {
                    label: "Go to 'home page'",
                    className: "btn-default",
                    callback: function() {
                        window.location.href = ARENA_PATH;
                    }
                },
                main: {
                    label: "go to your profile",
                    className: "btn-default",
                    callback: function() {
                        window.location.href = ARENA_PATH + "/profile";
                    }
                }
            }
        });
    }

}

function showSuccessCancelVote() {
    bootbox.dialog({
        message: "You successfully voted for canceling the match! Do you want to stay on the matchpage?",
        title: "Match result",
        buttons: {
            success: {
                label: "Stay on matchpage!",
                className: "btn-default",
                callback: function() {
                    window.location.reload();
                }
            },
            danger: {
                label: "Go to 'home page'",
                className: "btn-default",
                callback: function() {
                    window.location.href = ARENA_PATH;
                }
            },
            main: {
                label: "go to your profile",
                className: "btn-default",
                callback: function() {
                    window.location.href = ARENA_PATH + "/profile";
                }
            }
        }
    });
}

function sendPingNotification(that) {
    var user_id = $(that).attr("data-value");
    var match_id = getLastPartOfUrl();
    $.ajax({
        url: ARENA_PATH + '/notification/sendPing',
        type: "POST",
        dataType: 'json',
        data: {
            fake: false,
            user_id: user_id,
            match_id: match_id
        },
        success: function(result) {
            l(result);
            if (result.status == false) {
                $(that).removeClass("btn-default").addClass("btn-danger");
            } else {
                $(that).removeClass("btn-danger").addClass("btn-default");
            }
        }
    });
}

function matchVotePlayer(that) {
    var user_id = $(that).val();
    var match_id = getLastPartOfUrl();
    // Typ des Submits

    var type = $(that).attr("data-type");
    $(that).tooltip("hide");

    switch (type) {
        case "1": // upvote
            classType = "success";
            break;
        case "2": // downvote
            classType = "danger";
            break;
        default:
            return false;
            break;
    }
    $.ajax({
        url: ARENA_PATH + '/match/votePlayer',
        type: "POST",
        dataType: 'json',
        data: {
            fake: false,
            user_id: user_id,
            match_id: match_id,
            type: type
        },
        success: function(result) {
            l(result);
            if (result.status == true) {
                // switch Vote display
                html = "<span class='text-center text-" + classType + "'>voted!<span>";
                $(that).parent().html(html);

                // update html votecount on page
                decreaseVoteCount(type);

                // hide vote buttons if nesseccary
                hideVoteButtons(type);
            }
        }
    });
}

function submitCancelMatch() {
    var match_id = getLastPartOfUrl();

    // reset Fehlerstatus
    $("#leaverCancelMatchPannel input[type='checkbox']").css("color", "black");
    $("#checkErrorDiv").html("");

    // reason auslesen
    reason = $("#checkGroup label.active > input").val();
    l(reason);


    var leaverArray = getLeaverArray();

    if (leaverArray.length == 0 && reason == "1") {
        $("#leaverCancelMatchPannel input[type='checkbox']").css("color", "red");
        error = '<br><div class="alert alert-block alert-danger"><p>select at least one Player who didn\'t join the Match!</p></div>';
        $("#checkErrorDiv").html(error);
    } else {
        $.ajax({
            url: ARENA_PATH + '/match/cancelVote',
            type: "POST",
            dataType: 'json',
            data: {
                fake: false,
                match_id: match_id,
                leaverArray: leaverArray,
                votetype: reason
            },
            success: function(result) {
                l(result);
                $("#generalModal").modal("hide");
                showSuccessCancelVote();
            }
        });
    }

}

function hideVoteButtons(type_id) {
    l(type_id);
    var votes = getVoteCount(type_id);
    l("votes");
    l(votes);
    var buttons = new Object();
    if (votes <= 0) {
        buttons = $(".votebutton[data-type='" + type_id + "']");
    }
    l("buttons");
    l(buttons);
    if (buttons.length > 0) {
        $.each(buttons, function(keys, values) {
            l(values);
            $(values).hide();
        });
    }


}

function decreaseVoteCount(type) {
    // Typ des Submits
    value = getVoteCount(type);

    if (value > 0) {
        value = value - 1;
        $(elem).html(value);
    }
}

function getVoteCount(type) {
    switch (type) {
        case "1":
            elem = "#userUpvotesLeft";

            break;
        case "2":
            elem = "#userDownvotesLeft";
            break;
    }
    l("elem:" + elem);
    var value = parseInt($(elem).html());

    return value;
}

function getLeaverArray() {
    // LeaverVotes auslesen
    checkedInputs = $("#leaverCancelMatchPannel input[type='checkbox']:checked");
    // array zum uebergeben zusammenbauen
    var leaverArray = new Array();
    $.each(checkedInputs, function(index, value) {
        leaverArray.push($(value).val());
    });
    return leaverArray;
}

function checkScreenshotUploaded() {
    html = $("#screenshotUploaded").html();
    if (html != "") {
        return true;
    } else {
        return false;
    }
}

function showScreenshotError() {
    $("#checkScreenshotErrorDiv").removeClass("hide");
}

function removeScreenshotError() {
    $("#checkScreenshotErrorDiv").addClass("hide");
}