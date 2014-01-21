$(function() {
  // Handler for .ready() called.
  if (document.URL.indexOf("/events") >= 0) {
  	initEventButtons();
  }
});

function initEventButtons(){
	$("#joinEventButton").click(function(){
		joinEvent();
	});

    $("#signOutEventButton").click(function(){
        signOutOfEvent();
    });

    $("#goToEventButton").click(function(){
        goToEvent();
    });

    $(".goToSubEventButton").click(function(){
        goToSubEvent(this);
    });
}

/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
 function joinEvent() {
 	var idJoinButton = "#joinEventButton";
 	var disabled = $(idJoinButton).hasClass('disabled');
 	if (!disabled) {

 		var event_id = $(idJoinButton).attr("data-value");
 		var matchtype_id = $(idJoinButton).attr("data-mtid");
 		l(event_id);
                // timestamp kontrollieren ob er sich noch einschreiben darf
                var endTimestamp = $(idJoinButton).attr("data-time");
                l(endTimestamp);
                var now = $.now() / 1000;
                l(now);
                
                if (now <= endTimestamp) {
                	l("checkIfPlayerIsBanned Start");
                	$
                	.ajax({
                		url : 'find_match/checkJoinQueue',
                		type : "POST",
                		dataType : 'json',
                		data : {
                			matchtype_id: matchtype_id
                		},
                		success : function(result) {
                			l("checkIfPlayerIsBanned success");
                			l(result);
                			l(result.banned);
                			l(result.data);
                			var banCounts = result.banCounts;
                			var display = result.display;
                            switch(result.status){
                                case "banned":
                                bannedTillTimestamp = result.data.BannedTill;
                                bannedAtTimestamp = result.data.BannedAt;
                                bannedAt = new Date(
                                    bannedAtTimestamp * 1000)
                                .format('d.m.Y - h:i:s');
                                bannedTill = new Date(
                                    bannedTillTimestamp * 1000)
                                .format('d.m.Y - h:i:s');
                                l(bannedTill);

                                bannedBy = result.data.Reason+" - <img src='"+result.data.Avatar+"'><a href='profile/"+result.data.bannedBy+"'>"+result2.data.name+"</a>";

                                banReasonText = result.data.banReasonText;
                                text = "<div align='center'><h4>You got banned "
                                + bannedBy
                                + "</h4> "
                                + "<p>at "
                                + bannedAt
                                + " till "
                                + bannedTill
                                + "</p>"
                                +"<p class='well'>"
                                + banReasonText
                                +"</p>"
                                + "<h3>It is your "
                                + banCounts
                                + ". Ban!</h3>"
                                + "<p>Until then you cant join a Queue! Try be more mannered next time.</p></div>";

                                bootbox
                                .alert(
                                    text,
                                    function() {

                                    });
                            break;
                            case "inMatch":
                            bootbox.alert("You are already in a Match! Please check your notifications! (top right)", function() {
                                    // beim Verlassen der Seite eine Warnung anzeigen:
                                    // deaktivieren
                                    window.location = ARENA_PATH+"/openMatches";

                                });
                            break;
                            case true:
                            $
                            .ajax({
                                url : 'events/joinEvent',
                                type : "POST",
                                dataType : 'json',
                                data : {
                                    event_id : event_id
                                },
                                success : function(result) {
                                    l("initOnclickJoinEvent success");
                                    l(result);
                                    if(result.status){
                                        text = "<div align='center'><h4>You sucessfully singed-in to this Event</h4> "
                                        + "<p>you get a notification (top-right) if the Event is open. Then you can start and play your first Matches.</p>";
                                        bootbox.alert(text, function() {
                                            window.location.reload();
                                        });
                                    }
                                    else{
                                        text = "<div align='center'><h4 class='text-error'>You cant join this Event</h4> "
                                        + "<p>you do not fulfill the requirements</p>";
                                        bootbox.alert(text, function() {
                                            window.location.reload();
                                        });
                                    }

                                }
                            });
break;
}
}});
}
				// sonst anzeigen das er zu sp�t ist
				else {
					text = "<div align='center'><h4>Sorry, you are too late :(</h4> "
						+ "<p>you cant sign-in to this Event anymore. Try to be faster next time :)</p>";
bootbox.alert(text, function() {
	window.location.reload();
});
}



}
}

/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
 function signOutOfEvent() {

    idSingOutButton = "#signOutEventButton";
    l($(idSingOutButton));

    l("initOnclickSignOutEvent Start");

    var event_id = $(idSingOutButton).attr("data-value");
    l(event_id);
    // timestamp kontrollieren ob er sich noch einschreiben
    // darf
    var endSubmissionTimestamp = $(idSingOutButton).attr("data-time");
    // l(startEvent);
    var now = $.now() / 1000;
    l(now);
    if (now < endSubmissionTimestamp) {
        $
        .ajax({
            url : 'events/signOut',
            type : "POST",
            dataType : 'json',
            data : {
                event_id : event_id
            },
            success : function(result) {
                l("initOnclickSignOutEvent success");
                text = "<div align='center'><h4>You sucessfully singed-out of this Event</h4> "
                + "";
                bootbox.alert(text, function() {
                    window.location.reload();
                });
            }
        });

    }
    // zu sp�t darf nciht mehr sing outen
    else {
        text = "<div align='center'><h4>Sorry, you are too late :(</h4> "
            + "<p>you cant sign-out of this Event anymore. Now you have to play the Event!</p>";
            bootbox.alert(text, function() {
                window.location.reload();
            });
        }

        l("initOnclickSignOutEvent End");
    }

/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
 function goToEvent(button){
    var idGoToButton = "#goToEventButton";
    var eventID = $(idGoToButton).attr("data-value");
    var createdEventID = $(idGoToButton).attr("data-ce");
    l(eventID);
    l(createdEventID);
    if(createdEventID > 0){
        window.location = ARENA_PATH+"/event/"+eventID+"/"+createdEventID;
    }
    else{

    }
}

function goToSubEvent(that){
    button = $(that);
    var event_id = $(button).attr("data-value");
    var created_event_id = $(button).attr("data-ce");
    l(event_id);
    l(created_event_id);
    if(created_event_id > 0 && event_id > 0){
        window.location = ARENA_PATH+"/event/"+event_id+"/"+created_event_id;
    }
}