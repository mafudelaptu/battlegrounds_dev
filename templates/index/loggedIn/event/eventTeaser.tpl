{if $data|count > 0 && $data|is_array}
<div id="upcomingEvents" align="center" class="">
<!-- <h2><i class="icon-trophy"></i>&nbsp;Next Event</h2> -->
<div class="blackH2">
	<i class="icon-trophy"></i> Next<green>Event</green>
</div>
<!-- <img src="img/index/nextevent.jpg"> -->
	<div class="" style="height: 230px">
		<p>
			<span class="label label-inverse">{$data.Name} #{$data.EventID}</span> 
		</p>
		<div class="row-fluid">
		<div class="span6"><img src="img/prizes/prizeTypeID_{$nextEvent.PrizeTypeID}.png" class="t" title="{$nextEvent.PrizeCount}x {$nextEvent.PrizeName} {if $nextEvent.PrizeCost > 0}
						(max Cost:  {$nextEvent.PrizeCost}&euro;
						{/if}" width="100"></div>
		<div class="span6">{if $nextEvent.PointReq > 0 || $nextEvent.LeagueReq != ""}
							{if $nextEvent.PointReq > 0}
								<div class="pull-left">>= <span class="label t" title="minimum D2L-Points">{$nextEvent.PointReq}</span></div>
							{/if}
							{if $nextEvent.LeagueReq != ""}
								<div class="pull-left t" title="minimum League: {$nextEvent.LeagueReq}">{include file="prototypes/medalIcon.tpl" leagueNameSimple=$nextEvent.LeagueReq}</div>
							{/if}
						{else}
							no Requirements - everybody can join
						{/if}</div>
		</div>
		<div class="row-fluid" align="center">
			<div class="span4">
				<div>
					<strong>Starting at</strong>
				</div>
				<div style="font-size: 13px;">{$data.StartTimestamp|date_format:"%d.%m.
					- %H:%M"}</div>
			</div>
			<div class="span2">
				<div class="t" title="Match-Mode">
					<strong>MM</strong>
				</div>
				<div class="t badge badge-info" title="{$data.MatchModeName}">{$data.MMShortcut}</div>

			</div>
			<div class="span2">
				<div class="t" title="Region">
					<strong>RE</strong>
				</div>
				<div class="t badge badge-important" title="{$data.RegionName}">{$data.RShortcut}</div>
			</div>
			<div class="span2">
				<div class="t" title="Tournament-Type">
					<strong>TT</strong>
				</div>
				<div class="t badge badge-warning" title="{$data.TTName}">{$data.TTShortcut}</div>
			</div>
			<div class="span2">
				<div class="t" title="Min Players">
					<strong>MP</strong>
				</div>
				<div class="label">{$data.MinSubmissions}</div>
			</div>
		</div>
		<p>
		<script type="text/javascript">
				{literal}
				$(document).ready(function() {
					// create a new javascript Date object based on the timestamp
					// multiplied by 1000 so that the argument is in milliseconds, not seconds
					var date = new Date({/literal}{$data.StartSubmissionTimestamp}{literal}*1000);
					
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
		{if $smarty.now >= $data.StartSubmissionTimestamp}
		
				
				{assign "vielfaches" $eventSubsCount/$data.MinSubmissions}
				{if $vielfaches|floor == 0}
					{assign "vielfaches" "0"}
				{else}
					{assign "vielfaches" $vielfaches|floor}
				
				{/if}
			
			 <div class="btn btn-danger t" onclick="showSignedInPlayers();" title="{$eventSubsCount} Players already signed-in to this Event. Click to see which Players are already safe in the Event." style="line-height: 17px;">{$eventSubsCount} ({$vielfaches}) Players signed-in</div>
		
		{else}
			 	<div id="eventClock" style="height: 40px; padding-top: 3px;" class="t" title="time left till sign-in"></div>
			 {/if}
		
		</p>	
		
		<strong>Description:</strong>
		<div class="well well-small" style="overflow: auto; max-height: 30px; color:#000;">{$data.Description}</div>
	</div>
	<br><br><br>
	<div>
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
				{elseif $smarty.now >= $nextEvent.StartTimestamp}
					<button class="btn btn-block btn-success" id="goToEventButton" onclick="goToEvent();" 
					data-value="{$nextEvent.EventID}" data-value2="{$createdEventID}">Go to Event!</button>
				{/if}
		{else}
			<div class="alert alert-error">you dont fulfill the requirements for this Event</div>
		{/if}
		
	</div>

</div>
{/if}