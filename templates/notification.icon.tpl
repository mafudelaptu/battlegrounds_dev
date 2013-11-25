{if $count > 0}
		{assign "dataToggle" "data-toggle='dropdown'"}
		{assign var="label" value="label-important"}
		{assign "pfeilHide" ""}
	{else}
		{assign "count" "0"}
		{assign "data-toggle" ""}
		{assign "pfeilHide" "hide"}
{/if} 

<a id="dropNotification"
		role="button" class="dropdown-toggle pointer" {$dataToggle} style="height:22px"> 
<i class="icon-envelope icon-2x" style="margin-top: -8px"> 
	<span class="label {$label} notificationIconText"> 
		{$count} 
	</span>
</i>
<b class="caret {$pfeilHide}"></b>
</a>