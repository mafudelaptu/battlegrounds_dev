
<!-- Modal Submit Result -->
<div id="myModalSubmitResult" class="modal hide fade" tabindex="-1"
	role="dialog" aria-labelledby="myModalSubmitResult" aria-hidden="true">
	<form id="submitResultForm" name="submitResultForm"
		novalidate="novalidate" class="form-horizontal"
		action="{$_SERVER['PHP_SELF']}" method="POST">

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"
				aria-hidden="true">×</button>
			<h3 id="myModalLabel">Submit your Match result</h3>
		</div>


		<div class="modal-body">
			<div class="alert alert-error hide" data-dismiss="alert"
				id="myModalError">
				<a class="close" data-dismiss="alert" href="#">&times;</a> Please
				select, whether your team won or lost!
			</div>
			<div class="alert">
				<h4>Warning:</h4>
				you can't change your submission afterwards, please be sure you
				submit the right result!
			</div>

			<div class="control-group">
				<label class="control-label" for="checkWinLose">Your Team:</label>
				<div class="controls" id="checkWinLoseCheckboxDiv">
					<div class="btn-group" data-toggle="buttons-radio"
						id="checkWinLose">
						<button type="button" class="btn btn-success" value="won"
							onclick="setWinLoseValue(this);">Won</button>
						<button type="button" class="btn btn-danger" value="lost"
							onclick="setWinLoseValue(this);">Lost</button>
					</div>

					<input type="text" value="" id="checkWinLoseCheckboxHidden"
						name="checkWinLoseCheckboxHidden" class="hidden">
					<div id="checkWinLoseErrorDiv"></div>
				</div>
			</div>
			
<!-- 			<div class="control-group"> -->
<!-- 				<label class="control-label" for="inputDota2MatchID">Dota2-MatchID:<a -->
<!-- 					href="help.php#WhatIsDota2MatchID" target="_blank"><i -->
<!-- 						class="icon-question-sign t" title="What is the Dota2-MatchID?"></i></a></label> -->
<!-- 				<div class="controls"> -->
<!-- 					<input type="text" placeholder="7-8 diggits-long Dota2-MatchID..." -->
<!-- 						id="inputDota2MatchID" name="inputDota2MatchID"> -->
<!-- 				</div> -->
<!-- 			</div> -->
			<div class="control-group hide" id="screenshotUploadForm">
				<label class="control-label" for="screenshotUpload">Screenshot:<a
					href="help.php#ScreenshotUpload" target="_blank"><i
						class="icon-question-sign t"
						title="How should the Screenshot look like?"></i></a></label>
				<div class="controls">
					<input id="screenshotUpload" type="file" name="files"
						data-url="inc/jquery_fileUpload/server/php/" accept="image/*">
					<div id="progress" class="progress hide">
						<div class="bar" style="width: 0%;"></div>
					</div>
					<div id="fileUploaded"></div>
				</div>
			</div>
			<hr>
			<div id="leaverPannelToggler" style="text-align: center; margin-bottom:10px;">
				<button type="button" class="btn btn-warning" data-toggle="collapse"
					data-target="#leaverPannel">Players left the Game</button>
			</div>
			<div id="leaverPannel" class="collapse in" style="border:1px solid #f89406; background-color:#fcf8e3; padding:5px;">
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
		<div class="modal-footer">
			<button id="myModalCancelButton" class="btn" data-dismiss="modal"
				aria-hidden="true">Cancel</button>
			<button type="button" id="myModalSubmitButton"
				class="btn btn-success" onclick="submitResult()">Submit
				result!</button>
		</div>
	</form>
</div>