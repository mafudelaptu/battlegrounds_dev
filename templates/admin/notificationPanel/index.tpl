<div class="page-header">
	<h1>Notification Panel</h1>
</div>

{include "admin/notificationPanel/createNewNotification.tpl" notificationTypes=$notificationTypes}

{include "admin/notificationPanel/notifications.tpl" notifications=$notifications}

{*Modals*}
{include "admin/notificationPanel/modal/editNotificationModal.tpl"}