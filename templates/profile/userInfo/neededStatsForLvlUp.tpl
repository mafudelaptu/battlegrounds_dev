{if $skillBracketTypeID<6}
<style type="text/css">
.reqNeededStatsDesc {
	font-size: 10px;
	color: #999;
	text-align: center;
	line-height: 15px;
}

.reqNeededHeading {
	margin: -9px -8px 0 -9px;
	background-color: #292929;
	line-height: 20px;
	font-size: 9px;
 	font-family: Michroma;
	text-align: center;
	color:#ffffff;
}

.reqNeededHeading green{
	color:#9CDB04;
	font-weight: bold;
}

</style>
<div class="well well-small" style="padding-bottom: 14px; margin:0; ">
<div class="reqNeededHeading">
	Requirements you need for next Skill-Bracket: <green>{$data.NextSkillBracketName}</green>
</div>
<div class="row-fluid">
	<div class="span3">
		<div align="center" style="color:#b94a48"><span style="font-size:12px; font-weight:bold">{$data.nextTotalGames}+</span> <i class="icon-gamepad"></i></div>
		<div class="reqNeededStatsDesc">Games played</div>
		{if $data.nextTotalGames > 0 && $data.neededGames > 0}
			{math assign="width" equation="(( x/y ) * 100 )" x=$data.currentGames y=$data.nextTotalGames }
			{if $data.currentGames==0 || $width < 15}
				{assign "blackStyle" "color:black;"}
			{else}
				{assign "blackStyle" ""}
			{/if}
			
			<div class="progress" style="margin:0;">
			  <div class="bar" style="{$blackStyle} line-height: 21px; width: {$width}%;">{$data.currentGames}/{$data.nextTotalGames}</div>
			</div>
		{else}
			<div class="progress">
			  <div class="bar bar-success" style="line-height: 21px; width: 100%;">OK</div>
			</div>
		{/if}
	</div>
	<div class="span3">
		<div align="center"><span style="color:#f89406;font-weight:bold; font-size:12px;">{$data.nextWinRate}%+</span></div>
		<div class="reqNeededStatsDesc">Winrate</div>
		{if $data.neededWinRate > 0 && $data.nextWinRate > 0}
			{math assign="width" equation="(( x/y ) * 100 )" x=$data.currentWinRate y=$data.nextWinRate }
			{if $data.currentWinRate==0 || $width < 15}
				{assign "blackStyle" "color:black;"}
				{assign "descStyle" "width:111px;"}
			{else}
				{assign "blackStyle" ""}
				{assign "descStyle" ""}
			{/if}
			<div class="progress" style="margin:0;">
			  <div class="bar" style="{$blackStyle} line-height: 21px; width: {$width}%;"><div style="{$descStyle}">needed +{$data.neededWinRate} ({$data.nextWinRate} %)</div></div>
			</div>
		{else}
			<div class="progress" style="margin:0;">
			  <div class="bar bar-success" style="line-height: 21px; width:100%;">OK</div>
			</div>
		{/if}
	</div>
	<div class="span3">
		<div align="center"><i class="icon-thumbs-up"></i><span style="font-size:12px; font-weight:bold"> > 0</span></div>
		<div class="reqNeededStatsDesc">Credits</div>
		{if $data.neededCredits > 0}
			{math assign="width" equation="(( (x+20)/(y+20) ) * 100 )" x=$data.currentCredits y=$data.neededCredits }
			{if $data.currentCredits==-20 || $width < 15}
				{assign "blackStyle" "color:black;"}
			{else}
				{assign "blackStyle" ""}
			{/if}
			<div class="progress" style="margin:0;">
			  <div class="bar" style="{$blackStyle} line-height: 21px; width: {$width}%;">-{$data.neededCredits}</div>
			</div>
		{else}
			<div class="progress" style="margin:0;">
			  <div class="bar bar-success" style="line-height: 21px; width:100%;">OK</div>
			</div>
		{/if}
	</div>
	<div class="span3">
		<div align="center" style="color:#f89406;"><i class="icon-warning-sign"></i>&nbsp;<span class="t" title="active warns">{$data.activeWarns}</span>-<span class="t" title="total games needed to fullfill the criteria">{$data.neededWarnTotalGames}</span></div>
		<div class="reqNeededStatsDesc">Warns</div>
		{if $data.neededWarnGames > 0}
			{math assign="width" equation="(( y/x ) * 100 )" x=$data.neededWarnTotalGames y=$data.currentGames }
			{if $width < 15}
				{assign "blackStyle" "color:black;"}
			{else}
				{assign "blackStyle" ""}
			{/if}
			<div class="progress" style="margin:0;">
			  <div class="bar" style="{$blackStyle} line-height: 21px; width: {$width}%;">{$data.currentGames}/{$data.neededWarnTotalGames}</div>
			</div>
		{else}
			<div class="progress" style="margin:0;">
			  <div class="bar bar-success" style="line-height: 21px; width:100%;">OK</div>
			</div>
		{/if}
	</div>
</div>
</div>
{else}

{/if}

