{if $lastEvents|is_array && $lastEvents|count > 0}
<!-- <h2>Last Events</h2> -->
<h2><img src="img/events/lastevents.jpg"></h2>
{foreach key=k item=v from=$lastEvents name=data_array}
<div class="media">
	<div class="pull-left t upcomingEventTeaser"
		title="{$v.MatchModeName}-Tournament">
		<!-- <div class="upcomingEventTeaserDesc">{$v.MMShortcut}</div>
<!-- 		<div>Tournament</div> -->
		<img alt="" src="img/events/cmicon.jpg">
	</div>
	<div class="media-body">
		<h4 class="media-heading">{$v.Name} #{$v.EventID}</h4>
		{$v.Description}

		<div class="row-fluid">
			<div class="span6">
				<dl>
					<dt style="text-align: left">Prize for Winners</dt>
					<dd>
						<img src="img/prizes/prizeTypeID_{$nextEvent.PrizeTypeID}.png" class="t pull-left" title="{$nextEvent.PrizeCount}x {$nextEvent.PrizeName} {if $nextEvent.PrizeCost > 0}
						(max Cost:  {$nextEvent.PrizeCost}&euro;
						{/if}" width="100"><br>
						<div class="pull-left">{$nextEvent.PrizeCount}x <strong>{$nextEvent.PrizeName}</strong> 
						{if $nextEvent.PrizeCost > 0}
						(max Cost:  {$nextEvent.PrizeCost}&euro;)
						{/if}</div>
					</dd>
				</dl>
			</div>
			<div class="span6">
				<dl>
					<dt style="text-align: left">Requirements:</dt>
					<dd>
						{if $v.PointReq > 0 || $v.LeagueReq != ""} {if
						$v.PointReq > 0}
						<div class="pull-left">
							>= <span class="label t" title="minimum D2L-Points">{$v.PointReq}</span>
						</div>
						{/if} {if $v.LeagueReq != ""}
						<div class="pull-left t"
							title="minimum League: {$v.LeagueReq}">{include
							file="prototypes/medalIcon.tpl"
							leagueNameSimple=$v.LeagueReq}</div>
						{/if} {else} Free for all {/if}

					</dd>
				</dl>
			</div>
		</div>



		<h4>Details</h4>
		{include "events/eventDetails.tpl" data=$v userAlreadyInEvent=true
		eventSubsCount=$v.eventSubsCount} {if $v.createdEventsData|count > 0
		&& $v.createdEventsData|is_array} {foreach key=kc item=vc
		from=$v.createdEventsData name=createdEventData_array}
		<button class="btn btn-success pull-right"
			id="goToEventButton{$smarty.foreach.data_array.iteration}_{$smarty.foreach.createdEventData_array.iteration}"
			onclick="goToEvent(this.id);" data-value="{$v.EventID}"
			data-value2="{$vc.CreatedEventID}">Go to Sub-Event
			#{$vc.CreatedEventID}!</button>
		{/foreach} {/if} {if $v.eventSubsData|is_array &&
		$v.eventSubsData|count > 0}
		<div class="clearer"></div>
		<hr>
		<div class="btn-link" data-toggle="collapse"
			data-target="#lastEventsignedInPlayers{$smarty.foreach.data_array.iteration}">Signed-in
			Players ({$v.eventSubsCount})</div>

		<div
			id="lastEventsignedInPlayers{$smarty.foreach.data_array.iteration}"
			class="collapse">{include "events/signedInPlayerList.tpl"
			data=$v.eventSubsData eventSubsCount=$v.eventSubsCount nextEvent=$v}
		</div>
		{/if}

	</div>
</div>
{/foreach} {/if}
<br>
<br>

