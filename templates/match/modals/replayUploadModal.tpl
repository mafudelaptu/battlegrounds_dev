<div id="replayUploadModal" class="modal hide fade" tabindex="-1"
	role="dialog" aria-labelledby="replayUploadModal" aria-hidden="true">


	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal"
			aria-hidden="true">×</button>
		<h3 id="myModalLabel">Upload the parsed files of the replay</h3>
	</div>

	<div class="modal-body">
		<form id="submitResultForm" name="submitResultForm" class="form-horizontal"
			action="{$_SERVER['PHP_SELF']}" method="POST">
			<div class="control-group" id="screenshotUploadForm">
				<label class="control-label" for="screenshotUpload">select zip of
					replay-files:<a href="help.php#ScreenshotUpload" target="_blank"><i
						class="icon-question-sign t"
						title="How should the Screenshot look like?"></i>
				</a>
				</label>
				<div class="controls">
					<input id="replayUpload" type="file" name="files"
						data-url="inc/jquery_fileUpload/server/php/"
						accept="application/zip">
					<div id="progressReplay" class="progress hide">
						<div class="bar" style="width: 0%;"></div>
					</div>
					<div id="replayUploaded"></div>
				</div>
			</div>
		</form>
	</div>
	<div class="modal-footer">
		<button id="myModalCancelButton" class="btn" data-dismiss="modal"
			aria-hidden="true">Close</button>
	</div>

</div>
