<h2>Insert User</h2>
<form class="form-inline">
	<input type="text" value="" placeholder="SteamID"
		id="insertUserSteamID">
	<button class="btn" onclick="insertUserAdmin()" type="button">User einfügen</button>
	<span id="insertUserResposne"></span>
</form>
<form class="form-inline">
	User in Queue einfügen: 
	<button class="btn" onclick="insertRandomUserinQueue()" type="button">Random User, der noch nicht in der Queue ist, einfügen</button>
	<input type="text" value="1" placeholder="MatchTypeID"
		id="insertUserMatchTypeID">
	<span id="insertRandomUserinQueueResposne"></span>
</form>