<button type="button" class="btn"
	onclick="$('#nPCreateNewNewsArea').slideToggle()">
	<i class="icon-plus-sign"></i> Create a new News
</button>
<a href="http://www.quackit.com/html/online-html-editor/" target="_blank">Need help writing HTML? click here</a>
<div id="nPCreateNewNewsArea" class="hide">
	<h2>New Notification</h2>
	
	<div>
		<input type="text" placeholder="Title" id="nPTitle" name="nPTitle" />
	</div>
	<div>
		<textarea rows="10" style="width: 100%;" name="nPContent"
			id="nPContent"></textarea>
	</div>
	<div class="row-fluid">
		<div class="span3">
			<label for="nPOrder">Order of News</label>
			<input size="16" type="text" value="{$order}" id="nPOrder">
		</div>
		<div class="span3">
			<label for="nPShowDate">Show Date <small class="mute">*(0 = disabled)</small></label>
			<input class="datepicker" size="16" type="text" value="0" data-date-format="dd-mm-yyyy" id="nPShowDate">
		</div>
		<div class="span3">
			<label for="nPEndDate">End Date <small class="mute">*(0 = disabled)</small></label>
			<input class="datepicker" size="16" type="text" value="0" data-date-format="dd-mm-yyyy" id="nPEndDate">
		</div>
		<div class="span3">
			<label for="nPActive">Active:</label>
			<input type="checkbox" id="nPActive" checked="checked">
		</div>
	</div>

	<button type="button" class="btn btn-success"
		onclick="nPCreateNewNews()">Create!</button>

</div>
