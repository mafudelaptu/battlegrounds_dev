<div class="page-header">
	<h2>
		<i class="icon-play-circle"></i>&nbsp;Replay-Data
	</h2>
</div>

<div class="row-fluid">
	<div class="span6">
		<h3>Stats</h3>
		
		{if $replayData|is_array && $replayData|count > 0}
			{for $teamID=1 to 2}
				{if $teamID==1}
					{assign "teamName" "THE RADIANT"}
					{assign "tClass" "text-success"}
				{else}
					{assign "teamName" "THE DIRE"}
					{assign "tClass" "text-error"}
				{/if}
				
				{include "match/replay/teamTemplate.tpl" team=$teamName tClass=$tClass}
				<table class="table table-striped">
				{include "match/replay/tableHead.tpl"}
					<tbody>
				    {foreach key=k item=v from=$replayData name=data_array}
				    	{if $v.TeamID==$teamID || $v.TeamID == ""}
							{include "match/replay/statsTemplate.tpl" data=$v}
						{/if}
						
					{/foreach}
					</tbody>
				</table>
			{/for}
		{/if}
	</div>
	<div class="span6">
		<h3>Best Players By Category</h3>
		{if $replayData|is_array && $replayData|count > 0}
		<style type="text/css">
		<!--
		.bSBox{
			width:180px;
			margin: 0 10px 10px 0;
			display:inline-block;
		}
		.bSIcon{
			height:159px;
			
		}
		.bSValue{
			position:relative;
			top: 115px;
			margin:auto;
			width:150px;
			font-size: 24px;
			color: #FFF;
		}
		.bSTitle{
			background-color: #E0E0E0;
			height:20px;
			font-weight: bold;
			text-transform: capitalize;
			color: #3C3C3C;
			padding:0px;
		}
		.bSPlayers{
			text-align: center;
			font-weight: bold;
		}
		.bSPlayersBox{
			background-image: url(img/match/replay/bgPlayers.png); 
			background-repeat: no-repeat;
			padding:5px;
		}
		-->
		</style>
			{if $replayDataBestStats|is_array && $replayDataBestStats|count > 0}
				<div align="center">
					{for $key=1 to 5 }
						{include "match/replay/bestStatsTemplate.tpl" general=$replayDataBestStats[$key].general players=$replayDataBestStats[$key].players id=$key}
					{/for}
				</div>
				<div class="clearer"></div>
				<div align="center">
					{include "match/replay/bestStatsTemplate.tpl" general=$replayDataBestStats[$key].general players=$replayDataBestStats[$key].players id=6}
				</div>
				<div class="clearer"></div>
				<div align="center">
				{for $key=7 to 10 }
					{include "match/replay/bestStatsTemplate.tpl" general=$replayDataBestStats[$key].general players=$replayDataBestStats[$key].players id=$key}
				{/for}
				</div>
				<div class="clearer"></div>
			{/if}
		{/if}
	</div>
</div>
<h2>Replay-Chat</h2>
<div style="height:400px; overflow: auto;">
{if $replayChat|is_array && $replayChat|count > 0}
	{foreach key=k item=v from=$replayChat name=data_array}
		{if $v.SteamID > 0}
			<div class="row-fluid">
				<div class="span3" align="right">
					{$v.Time} - <a href="profile.php?ID={$v.SteamID}" target="_blank"><img src="{$v.Avatar}" alt="{$v.Name}'s Avatar"> {$v.Name}:</a>
				</div>
				<div class="span9">
					{$v.Msg}
				</div>
			</div>
		{else}
			<div class="row-fluid">
				<div class="span3" align="right">
					{$v.Time} - {$v.Player}:
				</div>
				<div class="span9">
					{$v.Msg}
				</div>
			</div>
		{/if}
	{/foreach}
{/if}
</div>

