
<style type="text/css">
.statsNumber {
	font-size: 18px;
	color: #333;
	font-weight: bold;
}

.statsDesc {
	font-size: 12px;
	color: #999;
}
</style>

{**
<div class="row-fluid">
	<div class="span2">
		<img src="img/3vs3Queue.png" />
		<div class="werbung">
			<script type="text/javascript">
			<!--
				google_ad_client = "ca-pub-8124404911103146";
				/* Dota2LeagueNew */
				google_ad_slot = "7634598442";
				google_ad_width = 125;
				google_ad_height = 125;
			//-->
			</script>
			<script type="text/javascript"
				src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
				
			</script>
		</div>
	</div>
	*}
	{**
	<div class="span10">
		{include "find_match/3vs3Queue/stats.tpl"}
		<hr>

		<div class="row-fluid">
			<div class="span6" align="center">{include
				"find_match/3vs3Queue/selectMatchmode.tpl"}</div>
			<div class="span6" align="center">{include
				"find_match/3vs3Queue/selectRegion.tpl"}</div>
		</div>
		<br> {if $openSubmitsLock}
		<button type="button"
			class="btn btn-large btn-block btn-success pull-right disabled t"
			title="You dont submitted a result of a previous Match before. Please check your open Matches!">JOIN</button>
		{else}
		<button type="button"
			class="btn btn-large btn-block btn-success pull-right"
			onclick="join3vs3Queue()">JOIN</button>
		{/if}


	</div>
</div>
*}
