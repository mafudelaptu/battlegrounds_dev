<h1>Ban a Player</h1>
<form class="form-inline">
	<input type="text" value="" placeholder="SteamID of Player"
		id="banSteamID">
	<textarea name="BanReasonText"
		placeholder="Ban Reason - why he gets a ban?" id="BanReasonText"
		style="width: 70%"></textarea>
	
	<div>
	<br>
		<button class="btn btn-danger" onclick="addBanToPlayer()"
			type="button">add a Ban to Player</button>
		<button class="btn" onclick="disableLastBan()" type="button">cancel/disable
			last active Ban of Player</button>
		<button class="btn btn-success" onclick="removeLastBan()"
			type="button">remove last Ban of Player</button>
	</div>
	<span id="banSteamIDResponse"></span>

</form>


<h1>Banned Players</h1>

{include "admin/banPanel/tablePrototype.tpl" data=$bannedPlayers
TableID="bannedPlayersTable"}

<h1>Perma Banned Players</h1>

{include "admin/banPanel/permaTablePrototype.tpl" data=$permaBannedPlayers
TableID="permaBannedPlayersTable"}
