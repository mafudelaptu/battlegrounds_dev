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

<script type="text/javascript">
{literal}
$(document).ready(function() {
	/*var leagueTypeID = {/literal}{$leagueData.LeagueTypeID}{literal};
	if(leagueTypeID == "1"){
		$('#quickJoinArea').block({
			message: "Available in Bronze-League and higher"
		});
	}
	*/
});
{/literal}
</script>

<div class="row-fluid">
	<div class="span2">
		<img src="img/stock_people.png" />
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
	<div class="span10">
		<h4>
			Queue-Stats
		</h4>
		<div class="row-fluid">
			<div class="span3">
				<div class="row-fluid">
					<div class="span3" align="center">
						<i class="icon-group icon-3x" style="color: #3a87ad"></i>
					</div>
					<div class="span9">
						<div class="statsNumber">{$singleQueueCount}</div>
						<div class="statsDesc">Players in Queue</div>
					</div>
				</div>

			</div>
			
			<div class="span3">
				<div class="row-fluid">
					<div class="span3" align="center">
						<i class="icon-gamepad icon-3x" style="color: #b94a48"></i>
					</div>
					<div class="span9">
						<div class="statsNumber">
						{if $openMatches|count > 0 && $openMatches|is_array}
							{* $singleQueueInMatchCount *} 
							 <a href="openMatches.php?guest=true&MTID=1">{$openMatches|count} (live)</a> 
						{else}
							0
						{/if}
						</div>
						<div class="statsDesc">Players in Match</div>
					</div>
				</div>

			</div>
		
			{if $matchModeStatsMaxMM != ""}
			<div class="span3">
				<div class="row-fluid">
					<div class="span3" align="center">
						<i class="icon-cog icon-3x" style="color: #f89406"></i>
					</div>
					<div class="span9">
						<div class="statsNumber">{$matchModeStatsMaxMM}
							({$matchModeStatsMaxMMCount})</div>
						<div class="statsDesc">Popular Matchmode now</div>
					</div>
				</div>
			</div>
			{/if}
			{if $regionStatsMaxR != ""}
			<div class="span3">
				<div class="row-fluid">
					<div class="span3" style="line-height: 45px;" align="center">
						<i class="icon-globe  icon-3x" style="color: #468847"></i>
					</div>
					<div class="span9">
						<div class="statsNumber">{$regionStatsMaxR}
							({$regionStatsRCount})</div>
						<div class="statsDesc">Popular Region now</div>
					</div>
				</div>

			</div>
			{/if}
		</div>
		<hr>
		{if $smarty.const.JUSTCM || $smarty.cookies.region == 7}
						<input type="hidden" id="qualiHiddenIdentifier" value="true">
						{if $openSubmitsLock}
							<button type="button"
								class="btn btn-large btn-block btn-success pull-right disabled t"
								title="You dont submitted a result of<br>a previous Match before. <br>Please check your open Matches!"
								style="line-height: 40px;">JOIN QUEUE</button>
							{else}
							<button type="button"
								class="btn btn-large btn-block btn-success"
								onclick="joinSingleQueue3(true,false,true)">JOIN QUEUE</button>
							{/if}
		{else}
			{if $leagueData.LeagueTypeID == "1"}
						<blockquote>
							<small class="muted">
								You are in the Qualifying-League. You can only join the <span class="badge badge-info">AP</span>-Queue.
							</small>
						</blockquote>
						<input type="hidden" id="qualiHiddenIdentifier" value="true">
						{if $openSubmitsLock}
							<button type="button"
								class="btn btn-large btn-block btn-success pull-right disabled t"
								title="You dont submitted a result of<br>a previous Match before. <br>Please check your open Matches!"
								style="line-height: 40px;">JOIN QUEUE</button>
							{else}
							<button type="button"
								class="btn btn-large btn-block btn-success"
								onclick="joinSingleQueue3(true)">JOIN QUEUE</button>
							{/if}
					{elseif $leagueData.LeagueTypeID == "2"}
					<div class="row-fluid">
						<div class="span6"
							style="padding-right: 30px; border-right: 1px solid #ccc;" id="quickJoinArea">
							<div class="row-fluid">
								<div class="span9">
									<li>Join automatically into AP, SD, RD and AR-Queue</li>
								</div>
								<div class="span3">
									<img src="img/quickJoin.png" />
								</div>
							</div>
							<div style="height: 14px;"></div>
							
							{if $openSubmitsLock}
							<button type="button"
								class="btn btn-large btn-block btn-success pull-right disabled t"
								title="You dont submitted a result of<br>a previous Match before. <br>Please check your open Matches!"
								style="line-height: 40px;">QUICK-JOIN</button>
							{else}
							<button type="button"
								class="btn btn-large btn-block btn-success pull-right"
								onclick="joinSingleQueue3(true)">QUICK-JOIN</button>
							{/if}
							
							
							<!-- For Rejoin -->
							<input name="openSubmitsLock" type="hidden"
								value="{$openSubmitsLock}" id="hiddenInputOpenSubmitsLock">
						</div>
						<div class="span6">
							<div class="row-fluid">
								<div class="span12" align="center">{include
									"find_match/singleQueueNew/selectMatchmode.tpl"}</div>
			<!-- 					<div class="span6" align="center">{include "find_match/singleQueueNew/selectRegion.tpl"}</div> -->
							</div>
							<br>
							<div align="center">
								<div class="btn-group">
									{if $smarty.const.DUOQUEUE}
										{assign "duoQueueFkt" "joinDuoSingleQueue2(false, false)"}
										{assign "duoDisabled" ""}
										{assign "duoDisableTitle" ""}
									{else}
										{assign "duoQueueFkt" ""}
										{assign "duoDisabled" "disabled"}
										{assign "duoDisableTitle" "temporarily disabled"}
									{/if}
									{if $openSubmitsLock}
									<button class="btn btn-large t disabled"
										onclick="joinSingleQueue3(false)"
										title="You dont submitted a result of<br>a previous Match before. <br>Please check your open Matches!">
			
										<i class="icon-user"></i>&nbsp;Single-Join
									</button>
									<button class="btn btn-large t disabled"
										onclick="{$duoQueueFkt}" title="You dont submitted a result of<br>a previous Match before. <br>Please check your open Matches!">
										<i class="icon-user"></i><i class="icon-user"></i>&nbsp;Duo-Join
										[Beta]
									</button>
									{else}
									<button class="btn btn-large" onclick="joinSingleQueue3(false)">
										<i class="icon-user"></i>&nbsp;Single-Join
									</button>
									
									<button class="btn btn-large t {$duoDisabled}" onclick="{$duoQueueFkt}" title="{$duoDisableTitle}"> 
										<i class="icon-user"></i><i class="icon-user"></i>&nbsp;Duo-Join
										[Beta]
									</button>
									{/if}
								</div>
							</div>
						</div>
					</div>
					{else}
					
					<input type="hidden" id="qualiHiddenIdentifier" value="true">
					<div class="row-fluid">
						<div class="span6"
							style="padding-right: 30px; border-right: 1px solid #ccc;" id="quickJoinArea">
							<div class="row-fluid">
								<div class="span9">
									<li>Join automaticly into <span class="badge badge-info">CD</span>-Queue</li>
								</div>
							</div>
							<div style="height: 14px;"></div>
							{if $openSubmitsLock}
							<button type="button"
								class="btn btn-large btn-block btn-success pull-right disabled t"
								title="You dont submitted a result of<br>a previous Match before. <br>Please check your open Matches!"
								style="line-height: 40px;">JOIN-CD</button>
							{else}
							<button type="button"
								class="btn btn-large btn-block btn-success"
								onclick="joinSingleQueue(true,false,false)">JOIN-CD</button>
							{/if}
							<!-- For Rejoin -->
							<input name="openSubmitsLock" type="hidden"
								value="{$openSubmitsLock}" id="hiddenInputOpenSubmitsLock">
						</div>
						<div class="span6">
							<div class="row-fluid">
								<div class="span12" align="center">{include
									"find_match/singleQueueNew/selectMatchmode.tpl"}</div>
			<!-- 					<div class="span6" align="center">{include "find_match/singleQueueNew/selectRegion.tpl"}</div> -->
							</div>
							<br>
							<div align="center">
								<div class="btn-group">
									{if $smarty.const.DUOQUEUE}
										{assign "duoQueueFkt" "joinDuoSingleQueue2(false, false)"}
										{assign "duoDisabled" ""}
										{assign "duoDisableTitle" ""}
									{else}
										{assign "duoQueueFkt" ""}
										{assign "duoDisabled" "disabled"}
										{assign "duoDisableTitle" "temporarily disabled"}
									{/if}
									{if $openSubmitsLock}
									<button class="btn btn-large t disabled"
										title="You dont submitted a result of<br>a previous Match before. <br>Please check your open Matches!">
										<i class="icon-user"></i>&nbsp;Single-Join
									</button>
									<button class="btn btn-large t disabled"
										title="You dont submitted a result of<br>a previous Match before. <br>Please check your open Matches!">
										<i class="icon-user"></i><i class="icon-user"></i>&nbsp;Duo-Join
										[Beta]
									</button>
									{else}
									<button class="btn btn-large" onclick="joinSingleQueue(false)">
										<i class="icon-user"></i>&nbsp;Single-Join
									</button>
									<button class="btn btn-large t {$duoDisabled}" onclick="{$duoQueueFkt}" title="{$duoDisableTitle}"> 
										<i class="icon-user"></i><i class="icon-user"></i>&nbsp;Duo-Join
										[Beta]
									</button>
									{/if}
								</div>
							</div>
						</div>
					</div>
					{/if}
		{/if}
		
	</div>
</div>
