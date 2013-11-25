<h2>fake Submits</h2>
<form class="form-inline">
	<input type="text" value="" placeholder="MatchID"
		id="fakeSubmittsMatchID">
	Welches Team soll gewinnen:<input type="radio" name="fakeSubmittsTeamWon" value="1" checked="checked"> Team 1  <input type="radio" name="fakeSubmittsTeamWon" value="2"> Team 2
	<button class="btn" onclick="fakeSubmittsSimulieren()" type="button">fake
		Submitts simulieren!</button>
	<span id="fakeSubmittsResposne"></span>
	<button class="btn" onclick="resetSubmissions()" type="button">fake User Einträge zurücksetzen</button>
	<span id="resetSubmissionsResposne"></span>
</form>

<form class="form-inline">
<input type="text" value="" placeholder="MatchID"
		id="submitAllMatchAcceptMatchID">
	<button class="btn" onclick="submitAllMatchAccept()" type="button">Alle (Fake)User Match bestätigen simulieren</button>
	<span id="submitAllMatchAcceptResposne"></span>
</form>