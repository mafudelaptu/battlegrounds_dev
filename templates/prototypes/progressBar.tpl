<style type="text/css">
.startLeagueLabel,.endLeagueLabel,.startLeagueBorder,.endLeagueBorder, .currentLeague {
	font-size: 10px;
	color: #333;
	line-height: 15px;
}

.startLeagueLabel {
	margin-left: 0px;
	width:45%;
}

.endLeagueLabel {
	margin-right: 0px;
	width:45%;
	text-align:right;
}

.startLeagueBorder {
	margin-left: 0px;
	font-weight: bold;
}

.endLeagueBorder {
	margin-right: 0px;
	font-weight: bold;
}

.progressBar {
	height: 50px;
	width: 100%;
}

.currentLeague{
	text-align: center;
	font-weight: bold;
	font-size: 12px;
}
</style>

<div id="progressBar{$id}" class="progressBar well well-small">
	<div class="startLeagueLabel pull-left t" title="Previous League-Level: {$startLeagueLabel}">
	{if $startLeagueLabel != ""}
		<i class="icon-double-angle-left"></i>&nbsp;previous League-Level: {$startLeagueLabel}
	{/if}
	</div>
	
	<div class="endLeagueLabel pull-right t" title="Next League-Level: {$endLeagueLabel}" >
	{if $endLeagueLabel != ""}
		next League-Level: {$endLeagueLabel}&nbsp;<i class="icon-double-angle-right"></i>
	{/if}	
	</div>
	<div class="clearer"></div>
	<div class="progress"
		style="line-height: 20px; margin: 0 25px; ">
		<div class="bar t" style="width: {$width}%; text-align: right; padding-right: 5px;" title="Your current D2L-Points: {$points}">{$points}</div>
	</div>
	<div class="row-fluid">
		<div class="span4"><div class="startLeagueBorder pull-left">{$startLeagueBorder}</div></div>
		<div class="span4"><div class="currentLeague t" title="You are currently in League: {$currentLeague}">{$currentLeague}</div></div>
		<div class="span4"><div class="endLeagueBorder pull-right">{$endLeagueBorder}</div></div>
	</div>
	
	
	
	
</div>