<style type="text/css">
.calendarDate {
	background-color: #fff;
	padding: 10px;
	font-weight: bold;
	color: #999;
	width: 22px;
	text-align: center;
}
</style>
{if $allEventTypes|is_array && $allEventTypes|count > 0}
<!-- <h3><i class="icon-calendar"></i>&nbsp;Calendar</h3> -->
<h3><img src="img/events/calendar.jpg"></h3>
<table class="table table-striped">
	<thead>
	<tr>
		<th>Day</th>
		<th>Prize</th>
		<th><span class="t" title="Requirements">Req</span</th>
		<th>Name</th>
		<th>Details</th>
	</tr>
	</thead>
	<tbody>
	{foreach key=k item=v from=$allEventTypes name=data_array}
		<tr>
			<td><span class="t" title="every {$v.StartDay}">{$v.StartDay|truncate:2:"":false}<br>{$v.StartTime} CET</span></td>
			<td>
				<img width="100" src="img/prizes/prizeTypeID_{$v.PrizeTypeID}.png" class="t" title="{$v.PrizeCount}x {$v.PrizeName} {if $v.PrizeCost > 0}
						(max Cost:  {$nextEvent.PrizeCost}&euro;)
						{/if}">
			</td>
			<td>
				{if $v.PointReq > 0 || $v.LeagueReq != ""}
							{if $v.PointReq > 0}
								<div class="pull-left">>= <span class="label t" title="minimum D2L-Points">{$v.PointReq}</span></div>
							{/if}
							{if $v.LeagueReq != ""}
								<div class="pull-left t" title="minimum League: {$v.LeagueReq}">{include file="prototypes/medalIcon.tpl" leagueNameSimple=$v.LeagueReq}</div>
							{/if}
						{else}
							no Req
						{/if}
			</td>
			<td>{$v.Name}</td>
			<td>
			<span class="t badge badge-info" data-original-title="{$v.MMName}">{$v.MMShortcut}</span>
				<span class="t badge badge-important" data-original-title="{$v.RegionName}">{$v.RShortcut}</span>
				<span class="t badge badge-warning"
					data-original-title="{$v.TTName}">{$v.TTShortcut}</span> <span
					class="t label" title="Minimum Players">{$v.MinSubmissions}</span>
			</td>
		</tr>
	{/foreach}
	</tbody>
</table>
{/if}