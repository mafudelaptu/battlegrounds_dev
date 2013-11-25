<!-- <div class="page-header"> -->
<!-- 	<h1>Wall Of Fame</h1> -->
<!-- </div> -->
<img src="img/index/WallOfFame.png">
<div class="row-fluid">
	<div class="span4">
		<!-- Beset Players by Elo -->
		{include file="index/loggedIn/wallOfFame/bestPlayers/bestPlayersByElo.tpl" data =
		$bestPlayer}
	</div>
	<div class="span4">
	<!-- last PLayed matches -->
		{include file="index/loggedIn/wallOfFame/lastMatches/lastMatches.tpl" data =
		$lastMatches}
</div>
	<div class="span4"><!-- last PLayed matches -->
		{include file="index/loggedIn/wallOfFame/highestCredits/highestCredits.tpl" data =
		$highestCredits}
	</div>
</div>


<!-- Wall of Fame -->
{include file="index/loggedIn/wallOfFame/wallOfFame.tpl" data =
$wallOfFameData}
