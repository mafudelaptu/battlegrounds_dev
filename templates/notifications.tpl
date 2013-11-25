{if $userLoggedIn == "true"}
<ul class="nav pull-right">
	<li id="notification-menu" class="dropdown">
	
		{include "notification.icon.tpl" count=$notificationCount steamID=$smarty.session.user.steamID}
		{*include "notification.icon.tpl" count=9 steamID=$steamID*}
		<ul class="dropdown-menu" role="menu" aria-labelledby="dropNotification">
			{include "notification.menu.tpl" data=$notificationData count=$notificationCount}
		</ul></li>
</ul>
{/if}