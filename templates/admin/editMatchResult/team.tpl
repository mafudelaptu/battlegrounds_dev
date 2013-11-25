{if $data|is_array && $data|count > 0}
	{if $teamID==1}
		{assign "teamClass" "alert-success"}
		{assign "teamName" "THE RADIANT"}
	{else}
		{assign "teamClass" "alert-error"}
		{assign "teamName" "THE DIRE"}
	{/if}
	<div class="{$teamClass}" align="center">
		{$teamName}
	</div>
	<div class="row-fluid" style="font-weight: bold">
		<div class="span3">Player</div>
		<div class="span2">SteamID</div>
		<div class="span2">Points @ Match</div>
		<div class="span4">Pointchanges</div>
		<div class="span1"></div>
	</div>
	{foreach key=k item=v from=$data name=data_array}
		<div class="row-fluid">
			<div class="span3">
				<img alt="Avatar" src="{$v.Avatar}" width="25" height="25">
				{include "prototypes/creditBasedName.tpl" name=$v.Name playerSteamID=$v.SteamID}
			</div>
			<div class="span2">{$v.SteamID}</div>
			<div class="span2">
<!-- 				<input class="t" title="Points before Match-Result" type="text" value="{$v.Points}" placeholder="Points before Match-Result" id="mRPointsBefore{$smarty.foreach.data_array.iteration}"> -->
			<strong>{$v.Points}</strong>
			</div>
			<div class="span4">
			
				{if $v.PointChanges|is_array && $v.PointChanges|count > 0}
					{foreach key=kk item=vv from=$v.PointChanges name=data_array2}
						{if $vv.PointsChange >= 0}
							{assign "pointsClass" "label-success"}
						{else}
							{assign "pointsClass" "label-important"}
						{/if}
						{if $smarty.foreach.data_array2.iteration !=1}
							&nbsp;+&nbsp;
						{/if}
						<div class="label {$pointsClass} t" title="{$vv.PointsTypeName}">{$vv.PointsChange}</div>
					{/foreach}
				{/if}
			
			= {$v.PointsChange}
			<button type="button" class="btn t" title="edit all Points of this User" onclick="mREditPointsChanges(this)" data-value="{$v.SteamID}"><i class="icon-pencil"></i></button>
			<button type="button" class="btn btn-danger t" title="delete all Points of this User" onclick="mRdeleteAllUserPoints(this)" data-value="{$v.SteamID}"><i class="icon-remove-sign"></i></button>
			
			</div>
			<div class="span1">
				{if $v.Leaver == 1}
					{assign "src" "../img/leaver.png"}
					{assign "width" $width}
					
					<img src="{$src}" width="{$width}" class="t pointer" title="delete Leaver Status" onclick="mRDemarkAsLeaver(this)" data-value="{$v.SteamID}">
				{else}
					{assign "src" "../img/leaver_preview.png"}
					{assign "width" $width}
					
					<img src="{$src}" width="{$width}" class="t pointer" title="Mark as Leaver" onclick="mRMarkUserAsLeaver(this)" data-value="{$v.SteamID}">
				{/if}
			</div>
		</div>
	{/foreach}
{/if}