<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
	aria-hidden="true">×</button>
	<h3 id="myModalLabel">Submit your Match result</h3>
</div>

<div class="modal-body">

	<div class="alert alert-warning">
		<h4>Warning:</h4>
		you can't change your submission afterwards, please be sure you
		submit the right result!
	</div>

	<div align="center">
		<div class="h2">Your Team:</div>
		<div class="alert alert-danger hide" data-dismiss="alert"
		id="checkWinLoseErrorDiv">
		Please
		select, whether your team won or lost!
	</div>
	<div class="btn-group btn-group-lg" data-toggle="buttons"
	id="checkWinLose">
	<label class="btn btn-success" for="checkWinLoseWon">
		<input type="radio" name="result" id="checkWinLoseWon" value="won"> Won
	</label>
	<label class="btn btn-danger" for="checkWinLoseLost">
		<input type="radio" name="result" id="checkWinLoseLost" value="lost"> Lost
	</label>
</div>
@if($matchData->matchtype_id != 2)
<br><br>
<div align="center">
	<button class="btn btn-warning" onclick="$('#leaverCancelMatchPannelArea').toggle();">Report a Leaver</button>
	<div id="leaverCancelMatchPannelArea" style="display:none">

		<h4>Select Player, who didn't join the Match</h4>
		<div id="leaverCancelMatchPannel">
			@for($i=1; $i<=2; $i++)
			<div class="pull-left" style="width:50%">
				@foreach($players[$i] as $player)
				@if(Auth::user()->id == $player['user_id'])
				<?php 
				$disabled = "disabled";
				?>
				@else
				<?php 
				$disabled = "";
				?>
				@endif
				<label class="checkbox" id="label_{{$player['user_id']}}"> 
					<input type="checkbox"
					value="{{$player['user_id']}}"
					id="player{{$player['user_id']}}"
					name="player{{$player['user_id']}}" {{$disabled}}>
					<img alt="Avatar" src="{{$player['avatar']}}" width="25" height="25">
					{{$player['name']}}
				</label>

				@endforeach
			</div>
			@endfor
		</div>
	</div>
</div>
<div class="clearer"></div>
@endif
<?php //dd($matchData); ?>
@if($matchData->matchtype_id == 2)
<br><br>
<div class="alert alert-danger hide" data-dismiss="alert"
		id="checkScreenshotErrorDiv">
		you have to upload a screenshot of the matchresult!
	</div>
<form class="form-horizontal" role="form" id="screenshotUploadForm">
	<div class="form-group">
		<label class="control-label col-sm-4" for="screenshotUpload">Screenshot:<a
			href="help.php#ScreenshotUpload" target="_blank"><i
			class="icon-question-sign t"
			title="How should the Screenshot look like?"></i></a></label>
			<div class="col-sm-7" id="screenshotUploadContent">
				<span class="btn btn-default fileinput-button">
					<i class="glyphicon glyphicon-plus"></i>
					<span>upload screenshot</span>
					<input id="screenshotUpload" type="file" name="files"
					data-url="../server/php/" accept="image/*">
				</span>
				<br><br>
				<div id="ScreenshotProgress" class="progress hide">
					<div class="progress-bar" style="width: 0%;"></div>
				</div>
				<div id="screenshotUploaded"></div>
			</div>
		</div>
	</form>
	@endif

</div>
</div>


<div class="modal-footer">
	<button id="myModalCancelButton" class="btn btn-default" data-dismiss="modal"
	aria-hidden="true">Cancel</button>
	<button type="submit" id="submitMatchResultButton"
	class="btn btn-success">Submit
	result!</button>
</div>
