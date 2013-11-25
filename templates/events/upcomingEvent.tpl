<style type="text/css">
.upcomingEventTeaser {
	background-color: #fff;
	padding: 10px;
	font-weight: bold;
	color: #333;
	width: 100px;
	text-align: center;
	height: 100px;
}

.upcomingEventTeaserDesc {
	line-height: 70px;
	font-size: 42px;
}

.prizeHeading {
	font-size: 17.5px;
	text-align: center;
}
</style>

<!-- <h2>Upcoming Event</h2> -->
<h2><img src="img/events/upcomingevent.jpg"></h2>
{if $nextEvent}
{* Ready handling *}
<script type="text/javascript">
{literal}
	$(document).ready(function() {
		if({/literal}{$nextEvent.EndSubmissionTimestamp}{literal} <= {/literal}{$smarty.now}{literal} && {/literal}{$nextEvent.StartTimestamp}{literal} >= {/literal}{$smarty.now}{literal} && {/literal}{$isRdyForEvent}{literal} == 0){
			l({/literal}{$nextEvent.EndSubmissionTimestamp}{literal}+" <= "+{/literal}{$smarty.now}{literal}+" && "+{/literal}{$nextEvent.StartTimestamp}{literal}+" >= "+{/literal}{$smarty.now}{literal}+" && "+{/literal}{$isRdyForEvent}{literal}+" == 0");
			$("#readyForEvent").modal("show").css({
			    width: "50%",
			    'margin-left': function () {
			        return -($(this).width() / 2);
			    }
			});
		}
	});
{/literal}
</script>

<div class="media">
	<div class="pull-left t upcomingEventTeaser"
		title="{$nextEvent.MatchModeName}-Tournament">
		<!--  <div class="upcomingEventTeaserDesc">{$nextEvent.MMShortcut}</div>
<!-- 		<div>Tournament</div> -->
		<img alt="" src="img/events/cmicon.jpg">
	</div>
	<div class="media-body">
		<h4 class="media-heading">{$nextEvent.Name} #{$nextEvent.EventID}</h4>
		{$nextEvent.Description}
		<div class="row-fluid">
			<div class="span4">
				<dl>
					{if $smarty.now < $nextEvent.StartSubmissionTimestamp}
						<dt style="text-align: left">Time till sign-in</dt>
						<dd>
						<script type="text/javascript">
						
						{literal}
							$(document).ready(function() {
								// create a new javascript Date object based on the timestamp
								// multiplied by 1000 so that the argument is in milliseconds, not seconds
								var date = new Date({/literal}{$nextEvent.StartSubmissionTimestamp}{literal}*1000);
								
								// hours part from the timestamp
								var hours = date.getHours();
								// minutes part from the timestamp
								var minutes = date.getMinutes();
								// seconds part from the timestamp
								var seconds = date.getSeconds();
								
								$('#eventClock').countdown({until: date, format: 'dHM'});
							});
							{/literal}
							
							</script>
							<div id="eventClock" style="height: 40px; padding-top: 3px;" class="t" title="time left till sign-in"></div>
							
						{elseif $smarty.now >= $nextEvent.StartSubmissionTimestamp && $smarty.now < $nextEvent.StartTimestamp}
							<dt style="text-align: left">Possible Time to Sign-in till Event starts</dt>
								<dd>
								<script type="text/javascript">
							
								{literal}
								$(document).ready(function() {
									// create a new javascript Date object based on the timestamp
									// multiplied by 1000 so that the argument is in milliseconds, not seconds
									var date = new Date({/literal}{$nextEvent.EndSubmissionTimestamp}{literal}*1000);
									
									// hours part from the timestamp
									var hours = date.getHours();
									// minutes part from the timestamp
									var minutes = date.getMinutes();
									// seconds part from the timestamp
									var seconds = date.getSeconds();
									
									$('#eventClock').countdown({until: date, format: 'dHM'});
								});
								{/literal}
								
								</script>
								<div id="eventClock" style="height: 40px; padding-top: 3px;" class="t" title="Possible Time to Sign-in till Event starts"></div>
								
						{elseif $smarty.now >= $nextEvent.StartTimestamp}
							<dt style="text-align: left">Event started</dt>
							<dd>Signed-in Players are playing the Tournament</dd>
						{/if}
					</dd>
				</dl>
			</div>
			<div class="span4">
				<dl>
					<dt style="text-align: left">Prize for Winners</dt>
					<dd>
						<img src="img/prizes/prizeTypeID_{$nextEvent.PrizeTypeID}.png" class="t pull-left" title="{$nextEvent.PrizeCount}x {$nextEvent.PrizeName} {if $nextEvent.PrizeCost > 0}
						(max Cost:  {$nextEvent.PrizeCost}&euro;
						{/if}" width="100">
						<div class="pull-left">{$nextEvent.PrizeCount}x <strong>{$nextEvent.PrizeName}</strong> 
						{if $nextEvent.PrizeCost > 0}
						(max Cost:  {$nextEvent.PrizeCost}&euro;)
						{/if}
						</div>
					</dd>
				</dl>
			</div>
			<div class="span4">
				<dl>
					<dt style="text-align: left">Requirements:</dt>
					<dd>
						{if $nextEvent.PointReq > 0 || $nextEvent.LeagueReq != ""}
							{if $nextEvent.PointReq > 0}
								<div class="pull-left">>= <span class="label t" title="minimum D2L-Points">{$nextEvent.PointReq}</span></div>
							{/if}
							{if $nextEvent.LeagueReq != ""}
								<div class="pull-left t" title="minimum League: {$nextEvent.LeagueReq}">{include file="prototypes/medalIcon.tpl" leagueNameSimple=$nextEvent.LeagueReq}</div>
							{/if}
						{else}
							Free for all
						{/if}
						
					</dd>
				</dl>
			</div>
		</div>

		<h4>Details</h4>
		{include "events/eventDetails.tpl" data=$nextEvent}
		
		{if $allowedToJoin}
			{if !$userAlreadyInEvent}
				{assign "startSubmission" $nextEvent.StartSubmissionTimestamp} 
				{if $userAlreadyInEvent || ($smarty.now <= $startSubmission || $smarty.now >= $nextEvent.EndSubmissionTimestamp)} 
					{assign "buttonClass" "disabled"}
					{assign "signInTitle" "sign-in possible at: `$nextEvent.StartSubmissionTimestamp|date_format:"%d.%m. - %H:%M"`"}
				{else} 
					{assign "buttonClass" "btn-primary"} 
					{assign "signInTitle" "join Event"} 
				{/if}
				<button class="btn btn-block {$buttonClass}" id="joinEventButton"
					data-time="{$nextEvent.EndSubmissionTimestamp}"
					data-value="{$nextEvent.EventID}" onclick="joinEvent();">{$signInTitle}</button>
			{elseif $smarty.now <= $nextEvent.EndSubmissionTimestamp}
				
					<button class="btn btn-block btn-danger {$disabled}"
					id="signOutEventButton" data-time="{$nextEvent.EndSubmissionTimestamp}"
					data-value="{$nextEvent.EventID}" onclick="signOutOfEvent()">Sign-Out of this Event</button>
			{elseif $smarty.now > $nextEvent.EndSubmissionTimestamp && $smarty.now <= $nextEvent.StartTimestamp}
				<button class="btn btn-block disabled"
				id="WaitingEventButton">The Event will be created soon! Please wait a moment.</button>
			{elseif $smarty.now >= $nextEvent.StartTimestamp}
					{if $createdEventID > 0}
						{if $isRdyForEvent == "1"}
								<button class="btn btn-block btn-success" id="goToEventButton" onclick="goToEvent(this.id);" 
							data-value="{$nextEvent.EventID}" data-value2="{$createdEventID}">Go to Event!</button>
						{else}
							<div class="alert alert-warning">You don't confirmed that you are ready to play the Event or declined it. Next time be prepared 10 minutes before Event start.</div>
						{/if}
					{else}
						<button class="btn btn-block disabled"
				id="WaitingEventButton">The Event will be created soon! Please wait a moment.</button>
					{/if}
					
					
			{/if}
		{else}
			<div class="alert alert-error">you dont fulfill the requirements for this Event</div>
		{/if}
		{if $eventSubsData|is_array && $eventSubsData|count > 0}
				<hr>
				<div class="btn-link" data-toggle="collapse"
					data-target="#signedInPlayers">Signed-in Players ({$eventSubsCount})</div>
		
				<div id="signedInPlayers" class="collapse in">
					{include "events/signedInPlayerList.tpl" data=$eventSubsData}
				</div>
		{/if}
	</div>

</div>
{else}
<div class="alert">No upcoming Event.</div>
{/if}