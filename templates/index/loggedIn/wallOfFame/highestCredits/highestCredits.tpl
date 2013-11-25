<!-- <h3>Highest Credits</h3> -->
<img src="img/index/highestcredits.jpg">
{if $data|count > 0 && $data|is_array}
<!-- <form class="form-inline"> -->
<!-- 		<label class="control-label" for="lastMatchesPlayedSelectCategory">Filtered by:</label> -->
<!-- 			<select name="lastMatchesPlayedSelectCategory" -->
<!-- 				id="lastMatchesPlayedSelectCategory"> -->
	
<!-- 				<option value="0">All Matchmodes</option> -->
<!-- 				{foreach key=k item=v from=$modi name=mmArray} -->
<!-- 					<option value="{$v.MatchModeID}">{$v.Name} ({$v.Shortcut})</option> -->
<!--     			{/foreach} -->
<!-- 			</select> -->
<!-- </form> -->
<div style="height:50px;">&nbsp;</div>
{/if}

<div id="lastMatchesPlayedTable">
	{include "index/loggedIn/wallOfFame/highestCredits/highestCreditsTable.tpl" data=$data steamID=$steamID}
</div>
