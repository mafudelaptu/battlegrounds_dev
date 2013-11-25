
<!-- Modal Submit Result -->
<div id="myModalCancelMatch" class="modal hide fade" tabindex="-1"
	role="dialog" aria-labelledby="myModalCancelMatch" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"
				aria-hidden="true">×</button>
			<h3 id="myModalCancelMatchLabel">Cancel Match</h3>
		</div>

		<div class="modal-body">
			<div class="alert alert-warn">
				<h4>Warning:</h4>
				you can't change your submission afterwards, please be sure you
				submit the right reason!
			</div>

			<div class="control-group">
				<label class="control-label" for="checkWinLose">Reason:</label>
				<div class="controls" id="checkWinLoseCheckboxDiv">
					<div class="btn-group" data-toggle="buttons-radio"
						id="checkGroup">
						<button type="button" class="btn active"
							onclick="$('#leaverCancelMatchPannelArea').toggle();" value="2">couldn't play Match/Match is broken!</button>
						<button type="button" class="btn"
							onclick="setWinLoseValue(this);$('#leaverCancelMatchPannelArea').toggle();" value="1">Player didn't join the match</button>
						
					</div>
					<div id="checkErrorDiv"></div>
				</div>
			</div>
			<div id="leaverCancelMatchPannelArea" style="display:none">
			
				<h4>Select Player, who didn't join the Match</h4>
				<div id="leaverCancelMatchPannel">
						<div class="pull-left" style="width:50%">
							{foreach key=k item=v from=$data.data.team1 name=team1_array} 
								{if $smarty.session.user.steamID == $v.SteamID}
									{assign "disabled" "disabled"}
								{else}
									{assign "disabled" ""}
								{/if}
								<label class="checkbox" id="label_{$v.SteamID}"> 
									<input type="checkbox"
									value="{$v.SteamID}"
									id="player{$v.SteamID}"
									name="player{$v.SteamID}" {$checked} {$disabled}>
									<img alt="Avatar" src="{$v.Avatar}" width="25" height="25">
									{$v.Name}
								</label> 
							{/foreach}
						</div>
						<div class="pull-left" style="width:50%">
							{foreach key=k item=v from=$data.data.team2 name=team2_array} 
							{if $smarty.session.user.steamID == $v.SteamID}
									{assign "disabled" "disabled"}
								{else}
									{assign "disabled" ""}
								{/if}
								<label class="checkbox" id="label_{$v.SteamID}"> 
									<input type="checkbox"
									value="{$v.SteamID}"
									id="player{$v.SteamID}"
									name="player{$v.SteamID}" {$checked}>
									<img alt="Avatar" src="{$v.Avatar}" width="25" height="25">
									{$v.Name}
								</label> 
							{/foreach}
					</div>
	
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button id="myModalCancelButton" class="btn" data-dismiss="modal"
				aria-hidden="true">Back</button>
			<button type="button" id="myModalCancelMatchSubmitButton"
				class="btn btn-success" onclick="submitCancelMatch()">Cancel Match!</button>
		</div>
</div>