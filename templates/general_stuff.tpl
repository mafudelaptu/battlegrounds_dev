 <!-- <div id="general_dialog" class="hidden"></div>
 <div id="queueArea" class="hide">
 		Searching for Match (Players found:<span class="badge" id="playersFound"></span>) <img src="img/ajax-loader.gif" width="126" height="22" alt="loading" />
    <button name="leaveQueue" class="btn-small btn-danger" data-toggle="modal" data-target="#myModalLeaveQueue">Leave Queue</button>
 </div> -->
 <div id="notification" class="alert alert-block alert-error fade in hide"><button type="button" class="close" onclick="$(this).parent().hide()">&times;</button><p></p></div>

{if $smarty.now >= $smarty.const.RELAUNCH}
{**
{literal}
<!-- UserVoice JavaScript SDK (only needed once on a page) -->
<script>(function(){var uv=document.createElement('script');uv.type='text/javascript';uv.async=true;uv.src='//widget.uservoice.com/SelND6rvpRCyPzVADgrmFA.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(uv,s)})()</script>
<!-- A tab to launch the Classic Widget -->

<script type="text/javascript">
UserVoice = window.UserVoice || [];
UserVoice.push(['showTab', 'classic_widget', {
  mode: 'feedback',
  primary_color: '#363636',
  link_color: '#007dbf',
  forum_id: 205948,
  tab_label: 'Feedback',
  tab_color: '#000000',
  tab_position: 'middle-right',
  tab_inverted: false
}]);
</script>
{/literal}
*}
{/if}
{* general  *}

{if $smarty.const.DEBUG == "true" OR $isAdmin == "1"}
<div class="alert alert-error" id="DEBUG_AREA">
<h4>DEBUG</h4>
<pre>{$smarty.server|print_r}</pre>
<pre>{$smarty.session.debug}</pre></div>
{/if}
