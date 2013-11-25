<!-- <h3>Best Players</h3> -->
<img src="img/index/bestplayers.jpg">
{if $data|count > 0 && $data|is_array}
<form class="form-inline">
<!-- 		<label class="control-label" for="bestPlayersSelectCategory">Filtered by:</label> -->
<!-- 			<select name="bestPlayersSelectCategory" -->
<!-- 				id="bestPlayersSelectCategory"> -->
<!-- 				<option value="general">General</option> -->
				{**
				{foreach key=k item=v from=$modi name=mmArray}
					<option value="{$v.MatchModeID}">{$v.Name} ({$v.Shortcut})</option>
    			{/foreach}
    			 *}
<!-- 			</select> -->
</form>
{/if}

<div id="bestPlayersTable">
	{include "index/loggedIn/wallOfFame/bestPlayers/bestPlayersTable.tpl" data=$data steamID=$steamID}
</div>
