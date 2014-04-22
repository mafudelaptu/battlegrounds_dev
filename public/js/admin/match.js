$(function() {
    if (document.URL.indexOf("/admin/matches") >= 0) {
        initButtons();
    }
});

function initButtons() {

    $("#insertFakeMatchSubmits").click(function() {
        var match_id = $("#fakeSubmittsMatchID").val();
        var teamWonID = $("input[name='fakeSubmittsTeamWon']:checked").val();
        $.ajax({
            url: ARENA_PATH + "/admin/matches/insertFakeMatchSubmits",
            type: "POST",
            dataType: 'json',
            data: {
                match_id: match_id,
                team_id: teamWonID
            },
            success: function(result) {
                l(result);
                $("#fakeSubmittsResposne").html(result.status);
            }
        });
    });

    $("#insertLeaverForMatch").click(function() {
        var user_id = $("#leaverUserID").val();
        var match_id = $("#leaverMatchID").val();
        $.ajax({
            url: ARENA_PATH + "/admin/matches/setLeaverForMatch",
            type: "POST",
            dataType: 'json',
            data: {
                user_id: user_id,
                match_id: match_id
            },
            success: function(result) {
                l(result);
                $("#insertLeaverForMatchResponse").html(result.status);
            }
        });
    });

    $("#removeLeaverForMatch").click(function() {
        var user_id = $("#leaverUserID").val();
        var match_id = $("#leaverMatchID").val();
        $.ajax({
            url: ARENA_PATH + "/admin/matches/removeLeaverForMatch",
            type: "POST",
            dataType: 'json',
            data: {
                user_id: user_id,
                match_id: match_id
            },
            success: function(result) {
                l(result);
                $("#insertLeaverForMatchResponse").html(result.status);
            }
        });
    });
}