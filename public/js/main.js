var debug = true;

$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        }
    });

    initRegionMenu();
    initTooltips();
    initPopovers();
    initTimeago();
    initDataTables();
});

function l(stuff) {
    if (debug) {
        console.log(stuff);
    }

}

function setConfirmUnload(on) {

    window.onbeforeunload = (on) ? unloadMessage : null;

    function unloadMessage() {

        return "Please do NOT close this Tab/the Browser or refresh this site! Always push 'Leave Queue' before you want to leave dota2-league.net, else you stay in Queue! This cause a bad experience on dota2-league.net. Thanks!";
    }

}

function setRegion(id) {
    $.ajax({
        url: ARENA_PATH + "/setRegion",
        type: "POST",
        dataType: 'json',
        data: {
            region: id,
        },
        success: function(data) {
            window.location.reload();
        }
    });
}

function initRegionMenu() {
    $("#regionMenu li>a").click(function() {
        region_id = $(this).attr("data-id");
        setRegion(region_id);
    });
}

function initTooltips() {
    $(".t").tooltip({
        container: "body"
    });
}

function initPopovers() {
    $("*[data-toggle=popover]").popover();
}

function initTimeago() {
    $(".timeago").timeago();
}

function initDataTables() {
    $(".datatable").dataTable({
        "sPaginationType": "bootstrap"
    });
}

function getLastPartOfUrl() {
    var url = $(location).attr('pathname');
    parts = url.split('/');
    //l(parts+" "+(parts.length-1));
    lastPart = parts[(parts.length - 1)];
    return lastPart;
}

function logoutDC(hash) {
    $.ajax({
        url: ARENA_PATH + "/logoutAjax",
        type: "POST",
        dataType: 'json',
        success: function(data) {
            window.location.href = ARENA_PATH + "/../forum/index.php?app=core&module=global&section=login&do=logout&k=" + hash;
        }
    });
}

function getParameterByName(name) //courtesy Artem
{
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regexS = "[\\?&]" + name + "=([^&#]*)";
    var regex = new RegExp(regexS);
    var results = regex.exec(window.location.href);
    if (results == null)
        return "";
    else
        return decodeURIComponent(results[1].replace(/\+/g, " "));
}

$.prototype.once = function() {
    var ret = this.not(function() {
        return this.once;
    });
    this.each(function(id, el) {
        el.once = true;
    });
    return ret;
};

function closeAllPopovers() {
    $("*[data-toggle=popover]").popover("hide");
}

Array.max = function(array) {
    return Math.max.apply(Math, array);
};
Array.min = function(array) {
    return Math.min.apply(Math, array);
};

jQuery.fn.dataTableExt.oSort['numeric-html-asc'] = function(a, b) {
    a = parseInt($(a).text());
    b = parseInt($(b).text());
    return ((a < b) ? -1 : ((a > b) ? 1 : 0));
};

jQuery.fn.dataTableExt.oSort['numeric-html-desc'] = function(a, b) {
    a = parseInt($(a).text());
    b = parseInt($(b).text());
    return ((a < b) ? 1 : ((a > b) ? -1 : 0));
};