{if $data|count > 0 && $data|is_array}
<div id="upcomingEvents" align="center" class="">
<!-- 	<h2><i class="icon-trophy"></i>&nbsp;Event is open</h2> -->
<div class="blackH2">
	<i class="icon-trophy"></i> Next<green>Event</green>
</div>
<!-- <img src="img/index/nextevent.jpg"> -->
	<div class="" style="height: 230px">
		<p>
			<span class="label label-inverse">{$data.Name}</span>
		</p>
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
		<br>
		<strong>Description:</strong>
		<p class="well well-small" style="overflow: auto; max-height: 90px; color:#000;">{$data.Description}</p>
	</div>
	<div>
		<button class="btn btn-block btn-success" id="goToEventButton" onclick="goToEvent(this.id);" data-value="{$nextEvent.EventID}" data-value2="{$createdEventID}">Go to Event!</button>
	</div>

</div>
{/if}