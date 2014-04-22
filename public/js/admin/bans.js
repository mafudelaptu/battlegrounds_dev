$(function() {
    if (document.URL.indexOf("/admin/bans") >= 0) {
        initBanButtons();
    }
});

function initBanButtons() {
    // Get Bans
    $("#bansGetUserButton").click(function() {
        var user_id = $("#getBansUserID").val();
        $.ajax({
            url: ARENA_PATH + "/admin/bans/getBansOfUser",
            type: "GET",
            dataType: 'json',
            data: {
                fake: false,
                user_id: user_id
            },
            success: function(result) {
                l(result);
                $("#bansGetUserResponse").html(result.status);

                $("#bansActiveTableArea").html(result.html_active);
                $("#bansOldTableArea").html(result.html_old);

                //init DataTable
                $("#bansTableActive").dataTable();
                $("#bansTableOld").dataTable();

            }
        });
    });
    // add a warn
    $("#bansAddBan").click(function() {
        var user_id = $("#bansUserID").val();
        var banreason = $("#bansBanReason").val();

        $.ajax({
            url: ARENA_PATH + "/admin/bans/addBan",
            type: "POST",
            dataType: 'json',
            data: {
                fake: false,
                user_id: user_id,
                banreason: banreason
            },
            success: function(result) {
                l(result);
                var html = result.status + " - user is now banned until " + result.bannedUntil + " (active bans: " + result.bansCount + ")";
                $("#bansResponse").html(html);
            }
        });
    });

    $("#bansRemoveLastBan").click(function() {
        var user_id = $("#bansUserID").val();
        var banreason = $("#bansBanReason").val();

        $.ajax({
            url: ARENA_PATH + "/admin/bans/removeLastBan",
            type: "POST",
            dataType: 'json',
            data: {
                fake: false,
                user_id: user_id,
                banreason: banreason
            },
            success: function(result) {
                l(result);
                var html = result.status;
                $("#bansResponse").html(html);
            }
        });
    });

    //Permaban
    $("#bansPermaban").click(function() {
        var user_id = $("#bansUserIDPermaban").val();
        var banreason = $("#bansBanReasonPermaban").val();

        $.ajax({
            url: ARENA_PATH + "/admin/bans/permaBan",
            type: "POST",
            dataType: 'json',
            data: {
                fake: false,
                user_id: user_id,
                banreason: banreason
            },
            success: function(result) {
                l(result);
                $("#bansResponsePermaban").html(result.status);
            }
        });

    });

    $("#bansRemovePermaban").click(function() {
        var user_id = $("#bansUserIDPermaban").val();
        var banreason = $("#bansBanReasonPermaban").val();

        $.ajax({
            url: ARENA_PATH + "/admin/bans/removePermaBan",
            type: "POST",
            dataType: 'json',
            data: {
                fake: false,
                user_id: user_id,
                banreason: banreason
            },
            success: function(result) {
                l(result);
                $("#bansResponsePermaban").html(result.status);
            }
        });
    });

    // Chat ban
    $("#bansChatBan").click(function() {
        var user_id = $("#bansUserID").val();
        var banreason = $("#bansBanReasonChat").val();

        $.ajax({
            url: ARENA_PATH + "/admin/bans/chatBan",
            type: "POST",
            dataType: 'json',
            data: {
                fake: false,
                user_id: user_id,
                banreason: banreason
            },
            success: function(result) {
                l(result);
                $("#bansResponseChat").html(result.status);
            }
        });

    });
}