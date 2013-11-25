
<div class="page-header">
	<h1>
		Event #{$eventID}: {$eventData.Name}<small>Sub-EventID: {$createdEventID}</small>
	</h1>
</div>
<div class="alert alert-warning"><strong>Feedback:</strong> please share your experience of this event feature in our <a href="http://www.n-gage.tv/forum/forumdisplay.php?fid=18">forum</a> </div>
<p>
{$eventData.Description}
</p>
<div class="row-fluid">
	<div class="span4">
		<h3>Event-Details</h3>
		<div class="controls controls-row">
			<div class="span5">
			<strong>Started at:</strong>
			</div>
			<div class="span7"> {$eventData.StartTimestamp|date_format:"%d.%m.
					- %H:%M"}</div>	
		</div>
		<div class="controls controls-row">
			<div class="span5">
			<strong>Tournament-Type:</strong>
			</div>
			<div class="span7">  <span class="badge badge-warning t" title="{$eventData.TTName}">{$eventData.TTShortcut}</span></div>	
		</div>
		<div class="controls controls-row">
			<div class="span5">
			<strong>Match-Mode:</strong>
			</div>
			<div class="span7"> <span class="badge badge-info t" title="{$eventData.MatchModeName}">{$eventData.MMShortcut}</span></div>	
		</div>
		<div class="controls controls-row">
			<div class="span5">
			<strong>Region:</strong>
			</div>
			<div class="span7">  <span class="badge badge-important t" title="{$eventData.RegionName}">{$eventData.RShortcut}</span></div>	
		</div>
		<div class="controls controls-row">
			<div class="span5">
			<strong>Total Players:</strong>
			</div>
			<div class="span7"><span class="label">{$eventData.MinSubmissions}</span></div>	
		</div>
	</div>
	<div class="span8">
		{include "event/playerStatus.tpl" data=$playerStatus}
	</div>
</div>
{* include "event/tournamentBracketJquery.tpl" *}
{include "event/tournamentBracket.tpl"}

<div id="eventChat" style="width:600px;margin:auto">
<!-- Chat includen -->
{*include file="event/chat/index.tpl"*}
{include "prototypes/chat.tpl" chatID="eventChat"}
</div>
				



