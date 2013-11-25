<button type="button" class="btn"
	onclick="$('#nPCreateNewNotificationArea').slideToggle()">
	<i class="icon-plus-sign"></i> Create a new Notification
</button>

<div id="nPCreateNewNotificationArea" class="hide">
	<h2>New Notification</h2>
	<div>
		<select name="nPNotificationTypeSelect" id="nPNotificationTypeSelect">
			{html_options options=$notificationTypes selected=3}
		</select>
	</div>
	<div>
		<input type="text" placeholder="Notification Text (HTML usable)"
			id="nPText" name="nPText" style="width: 100%" />
	</div>
	<div>
		<input type="text" placeholder="Link to (optional)" id="nPLink"
			name="nPLink" style="width: 100%" />
	</div>
	<div>
		Active: <input type="checkbox"
			id="nPActive" checked="checked">
	</div>
	<br>
	<br>
	<button type="button" class="btn btn-success"
		onclick="nPCreateNewNotification()">Create!</button>

</div>
