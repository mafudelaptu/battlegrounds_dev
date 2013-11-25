<div>
		<select name="nPNotificationTypeSelect" id="nPENotificationTypeSelect">
			{html_options options=$notificationTypes selected=$data.NotificationTypeID}
		</select>
	</div>
	<div>
		<input type="text" placeholder="Notification Text (HTML usable)"
			id="nPEText" name="nPEText" style="width: 100%" value="{$data.Text}" />
	</div>
	<div>
		<input type="text" placeholder="Link to (optional)" id="nPELink"
			name="nPELink" style="width: 100%"  value="{$data.Href}"/>
	</div>
	<div>
		Active: {if $data.Active==1}
				{assign "checked" "checked='checked'"}
			{else}
				{assign "checked" ""}
			{/if}
			<input type="checkbox" id="nPEActive" {$checked}>
	</div>