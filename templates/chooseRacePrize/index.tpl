<div class="page-header"><h1>Choose your Prize</h1></div>

{if $winnerData|count > 0 && $winnerData|is_array}
	{if $winnerData.Position == $pickPosition}
		{assign "allowedToChoose" true}
		<h2>You may now choose your Prize!</h2>
	{else}
		{assign "allowedToChoose" false}
		<h2>
			The current Pick-Position is: <strong>{$pickPosition}</strong>
		</h2>
		<blockquote>
			<p>
			Your placement in the Race is: <strong>{$winnerData.Position}</strong>
			</p>
			<p>
			Please wait until your Pick-Position is reached. Therefore refresh this page to check when your Pick-Position is reached.
		</p>
		</blockquote>
		
		
	{/if}
<br>
	<button class="btn btn-success saveSelection hide" onclick="saveChoosePrizeSelection()">save your selected Prize</button>
	<div  id="prizePool">
		{include "races/prizePool.tpl" choose=$allowedToChoose}
	</div>
	<br>
	<button class="btn btn-success saveSelection hide">save your selected Prize</button>
{else}
	<div class="alert">
		You are not allowed to see this page anymore.
	</div>
	
{/if}