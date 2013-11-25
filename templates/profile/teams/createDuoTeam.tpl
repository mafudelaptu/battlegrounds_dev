<div class="greenH2">Create a Duo-Team</div>
<div class="alert alert-info">
	<strong>NOTICE:</strong> Because of balancing-issues you just can select a Duo-Partner who is also in your League
</div>
<div id="createDuoTeamError" class="alert alert-error hide"></div>
<!--Suchschlitz zum user suchen -->
<form class="form-horizontal"
	style="margin-top: 10px">
	<div class="control-group">
		<label  class="control-label"for="duoTeamName"> Teamname: </label>
		<div class="controls">
			<input id="duoTeamName" name="duoTeamName" type="text"
				placeholder="Teamname" />
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="searchForPlayerInput"> Search for Player: </label>
		<div class="controls">
			<input id="searchForPlayerInput" type="text"
				placeholder="Steam-Name or SteamID">
		</div>
	</div>

	<!-- wird durch JS bef�llt -->
	<div id="searchForPlayerResults"></div>
	
	<div class="clearer"></div>
	<div class="control-group">
		
		<div class="controls">
			<button class="btn btn-success" onclick="createDuoTeam()" type="button">Create
		Duo-Team</button>
		</div>
	</div>
	<p>
	
		<p>
</form>
