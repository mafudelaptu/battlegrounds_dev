{if $steamID == $smarty.session.user.steamID}
	{if $count > 0}
		{assign var="iconClass" value="icon-envelope-alt"}
		{assign var="label" value="label-important"}
		<script type="text/javascript">
		$(document).ready(function() {
			  // Popover initialisieren
			  $("#userNotification").popover({
				  html:true,
				  placement:"right",
				  trigger: "click",
				  title: "Notifications",
				  content: function() {
				      return $('#userNotificationData').html();
				    }
			  });
			  
			});
		</script>
	{else}
		{assign var="iconClass" value="icon-envelope"}
		{assign var="label" value=""}
	{/if}
	
<div style="margin:10px 0;">
	<i class="{$iconClass} icon-2x t pointer" style="display:inline-block" id="userNotification" title="Notifications">
		<span class="label {$label} notificationIconText">{$count}</span>
	</i>
</div>	
<div id="userNotificationData" class="hide">
<small>
	{if $data|count > 0 && $data|is_array}
	<ul>
	  {foreach key=k item=v from=$data name=modi_array}
	  	<li>
	  		{if $v.href != ""}
	  			<a href="{$v.href}">{$v.count}</a>
	  		{else}
	  			{$v.count}
	  		{/if}
	  		&nbsp;{$v.message}
	  	</li>
	  {/foreach}
	</ul>
	{/if}
</small>
</div>
{/if}