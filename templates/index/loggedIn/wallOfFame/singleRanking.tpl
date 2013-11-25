{if $rankingName == "diamond"}
	
	{assign "borderColor" "#08C"}
	{assign "desc" "Diamond"}
{elseif $rankingName == "platinum"}

{assign "borderColor" "silver"}
{assign "desc" "Platinum"}

{elseif $rankingName == "gold"}

	{assign "borderColor" "gold"}
	{assign "desc" "Gold"}

{elseif $rankingName == "silver"}

	{assign "borderColor" "silver"}
	{assign "desc" "Silver"}

{elseif $rankingName == "bronze"}

	{assign "borderColor" "peru"}
	{assign "desc" "Bronze"}
{elseif $rankingName == "quali"}

	{assign "borderColor" "black"}
	{assign "desc" "Qualifying"}
{/if}
	<div><strong>Best {$desc} Player</strong></div>
<a href="profile.php?ID={$rankingData.SteamID}">
	<div
		style="background-image: url({$rankingData.AvatarFull}); width: 164px; height: 184px; border: 2px solid {$borderColor}; border-radius:5px;">

		<div style="height: 30px; background-color: {$borderColor}; margin:154px 0 0 0; line-height:30px; color: whiteSmoke;">{$rankingData.Name}</div>
	</div>
	</a>

<div class="row-fluid" style="width: 190px;">
	<div class="span5">
		<span class="t" title="D2L-Points of {$rankingData.Name}"><strong>Points: {$rankingData.Points}</strong></span>
	</div>
	<div class="span7" style="font-size: 11px; line-height: 22px;">
		<span class="text-success t"
			title="General Wins of {$rankingData.Name}">{$rankingData.Wins}</span> - 
		<span class="text-error t"
			title="General Losses of {$rankingData.Name}">{$rankingData.Loses}</span> - 
		<span class="text-warning t"
			title="General Win Rate of {$rankingData.Name}">{$rankingData.WinRate}%</span>
	</div>
	
</div>

