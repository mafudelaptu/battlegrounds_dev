<div class="blackH2">N-GAGE<green>RACES</green></div>
<!-- <img src="img/races/n-gageraces.jpg"> -->
{if $raceData|count > 0 && $raceData|is_array}

<!-- <h2> -->
	 {* <i class="icon-gift"></i> {$raceData.Name} *}
<!-- </h2> -->
<div class="greenH2">Open Race #{$raceData.RaceID}</div>
<!-- <img src="img/races/openingrace.jpg"> -->

<div class="row-fluid"  style="margin: 10px 0 0 0;">
	<div class="span3">
		{include "races/winnerList.tpl"}
	</div>
	<div class="span9">

		{include "races/raceInfo.tpl"}
						
		<div>
			<strong>What is a N-GAGE-Race?</strong>
		
		<p>The N-GAGE-Race is an interactive competition over time
			(currently about a month). In the Race-month the players with the most earned Points in DotA.N-GAGE will get Prizes. 
			The winners list expands automatically, depending on how many players are playing actively on Dota2-League. 
			If the current date exceeds the end date of the races, the winners will be determined. 
			The player with the most earned Points in that time stands on top.
			</p>
		<strong>How will the number of winners determined?</strong>
			{if $raceData.WinnerCountType == "Percent"}
				<p>
				The number of winners will be determined as follows:<br> 
					
					<strong>Number of Winners = </strong> (round up) (number of active players / 100) * {$raceData.WinnerCount}  
				</p>
			{/if}
			
		<strong>How will the Prizes distributed?</strong>
		{if $raceData.PrizeAwarding == "Wichteln"}
		<p>
			For this N-GAGE-Race there is a Prize-Pool (see below). Once the race has come to end and the winners have been determined, 
			the player on the first position may choose a prize. The selected price is removed from the prize pool and the player on the second position 
			may choose a prize and so on. 
		</p>
		<p>
			The player of the Winner-list will get a Notification (top right) after the race ended. There will be a Link to a 
			Prize-Choose-Page where you able to choose your prize you want out of the current Prize-Pool. But the Prize-Pick-Page is only for the player 
			unlocked when all players have selected a prize before. <br>
			Once a Prize has been successfully selected, the player will receive a friend request from "Dota2LeaguePrizes". For receiving the Prize you have to accept this friend request. 
			"Dota2LeaguePrizes" will want to trade the choosen Prize with the player. The Trade-request may take minutes, several hours or days. Please be patient.
		</p>
		{/if}
		</div>
		{if $raceData.PrizeAwarding == "Wichteln"}
			{include "races/prizePool.tpl"}
		{/if}
		</div>
</div>

{else}
<div class="alert">Sorry, there is no N-GAGE-Race active at the moment!</div>

{/if}