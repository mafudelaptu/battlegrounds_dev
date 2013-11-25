{if $data|count > 0 && $data|is_array}
<div id="upcomingEvents">
{* Ready handling *}
<script type="text/javascript">
{literal}
	$(document).ready(function() {
		if({/literal}{$nextEvent.EndSubmissionTimestamp}{literal} <= {/literal}{$smarty.now}{literal} && {/literal}{$nextEvent.StartTimestamp}{literal} >= {/literal}{$smarty.now}{literal} && {/literal}{$isRdyForEvent}{literal} == 0){
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
<!-- 	<h4 align="center"><i class="icon-trophy"></i>&nbsp;Also signed-in Players</h4> -->
<div class="blackH2">
	<i class="icon-trophy"></i> Next<green>Event</green>
</div>
<!-- 	<img src="img/index/nextevent.jpg"> -->
	<div class="" style="//height: 230px">
		<p align="center">
			<span class="label label-inverse">{$nextEvent.Name}  #{$nextEvent.EventID}</span>
		</p>
		<div class="row-fluid" align="center">
			<div class="span3 t " title="Starting at">
				<div>
					<small><strong>SA</strong></small>
				</div>
				<div>
					<span class="label">{$nextEvent.StartTimestamp|date_format:"%H:%M"}</span>
				</div>
			</div>
			<div class="span3">
				<div>
					<small><strong>Players</strong></small>
				</div>
				<div>
					<span class="label">{$eventSubsCount}</span>
				</div>
			</div>
			<div class="span3 t" title="Reached Minimum Signed-in Player Count">
				<div>
					<small><strong>RMC</strong></small>
				</div>
				<div>
					{if {$eventSubsCount} >= $nextEvent.MinSubmissions}
						{assign "reachedText" "yes"}
						{assign "reachedClass" "success"}
					{else}
						{assign "reachedText" "no"}
						{assign "reachedClass" "important"}
					{/if}
					<span class="label label-{$reachedClass}">{$reachedText}</span>
				</div>
			</div>
			<div class="span3">
				<div>
					<small><strong>Stacks</strong></small>
				</div>
				<div>
					{assign "vielfaches" $eventSubsCount/$nextEvent.MinSubmissions}
						{if $vielfaches|floor == 0}
							{assign "vielfaches" ""}
						{else}
							{assign "vielfaches" $vielfaches|floor}
						
						{/if}
					<div class="label t" title="{$vielfaches} parallel Events will be created">{$vielfaches}</div>
				</div>
			</div>
		</div>
		<div style="background-color:white; color:#333; overflow: auto; max-height: 143px;">{include
			"events/signedInPlayerList.tpl" data=$data}</div>
		<br>
		{if $smarty.now > $nextEvent.EndSubmissionTimestamp &&  $smarty.now <= $nextEvent.StartTimestamp}
				<button class="btn btn-block disabled"
				id="WaitingEventButton">The Event will be created soon! Please wait a moment.</button>
		{else}
		<button class="btn btn-block btn-danger {$disabled}"
			id="signOutEventButton" data-time="{$nextEvent.StartTimestamp}"
			data-value="{$nextEvent.EventID}" onclick="signOutOfEvent()">Sign-Out of this Event</button>
		{/if}

	</div>
</div>
{/if}
