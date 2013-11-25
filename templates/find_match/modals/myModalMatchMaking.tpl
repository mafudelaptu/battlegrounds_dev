<div id="myModalMatchMaking" class="modal hide fade" tabindex="-1"
	role="dialog" aria-labelledby="myModalMatchMaking" aria-hidden="true">
	<div class="modal-header">
<!-- 		<h3 id="myModalMatchMakingLabel">Matchmaking <a href="help.php#HowDota2LeagueWorks" target="_blanc" class="t" title="How does this work?"><i -->
<!-- 							class="icon-question-sign"></i></a> -->
<!-- 		</h3> -->
<!-- 			<img src="img/find_match/matchmaking.jpg"> -->
		<div class="blackH2">
			{if $smarty.const.NOLEAGUES==false}
				<div class="pull-right" style="font-size:14px; width: 400px;text-align: right; text-transform: none;">Your Pool: <green id="userPoolSpan"></green></div>		
			{/if}
			MATCH<green>MAKING</green></div>
	</div>
	
	<div class="modal-body">
		{if $smarty.const.NOLEAGUES==false}
		<div class="row-fluid">
			<div class="span3">
				<div class="row-fluid">
					<div class="span3" align="center">
						{include "prototypes/skillBracketIcon.tpl" skillBracketTypeID=1 skillBracketName=Prison}
					</div>
					<div class="span9">
						<div class="statsNumber" id="prisonQueueCount">0</div>
						<div class="statsDesc">Prison Player(s) in Queue</div>
					</div>
				</div>

			</div>
			<div class="span3">
				<div class="row-fluid">
					<div class="span3" align="center">
						{include "prototypes/skillBracketIcon.tpl" skillBracketTypeID=2 skillBracketName=Trainee}
					</div>
					<div class="span9">
						<div class="statsNumber" id="traineeQueueCount">0
						</div>
						<div class="statsDesc">Trainee Player(s) in Queue</div>
					</div>
				</div>

			</div>
			<div class="span3">
				<div class="row-fluid">
					<div class="span3" align="center">
						<div class="pull-left" style="line-height: 30px;font-size: 18px;font-weight: bold;">>=</div>{include "prototypes/skillBracketIcon.tpl" skillBracketTypeID=3 skillBracketName=Amateur}
					</div>
					<div class="span9">
						<div class="statsNumber" id="amateurOrHigherQueueCount">0</div>
						<div class="statsDesc">Amateur or higher Player(s) in Queue</div>
					</div>
				</div>
			</div>
			<div class="span3">
				<div class="row-fluid">
					<div class="span3" style="line-height: 45px;" align="center">
						<i class="icon-unlock-alt icon-2x"></i>
					</div>
					<div class="span9">
						<div class="statsNumber" id="forceQueueCount">0</div>
						<div class="statsDesc">Player(s) in Force-Queue</div>
					</div>
				</div>

			</div>
		</div>
		<hr>
		{*include "find_match/modals/liveGamesTicket.tpl" data=$param*}
		{/if}
		<div class="row-fluid">
			<div class="span3" align="center">
<!-- 			<div class="alert alert-warn"><strong>Hint:</strong> for faster matchmaking - select more matchmodes!</div> -->
		
				<p>
					<img src="img/searching.gif" width="100" alt="loading" />
				</p>
				<h4>
					<span id="matchMakingClock"></span>
				</h4>
				<p>
					<label class="checkbox" class="t"
						title="enlarge your elo-search-range"> <input
						type="checkbox" name="forceSearching" id="forceSearching" checked="checked">
						force Searching <a href="help.php#forceSearching" target="_blanc"><i
							class="icon-question-sign"></i></a>
					</label>
				</p>
				<script type="text/javascript">
				<!--
					google_ad_client = "ca-pub-8124404911103146";
					/* Dota2League-MatchMakingModal */
					google_ad_slot = "7749020847";
					google_ad_width = 200;
					google_ad_height = 200;
				//-->
				</script>
				<script type="text/javascript"
					src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
					
				</script>
<!-- 				<a href="help.php#HowKnowMatchFound" target="_blanc" class="t" title="How will I know when a match is found?"><i -->
<!-- 							class="icon-question-sign"></i> How will I know when a match is found?</a> -->
				<div class="alert alert-error">Can't hear sound notification? <p>Test it <a href="soundPlugin.php" target="_blank">here</a></p></div>
<!-- 				<div class="alert alert-info"><strong>For better communication:</strong> join in-game chat-channel <i>dota2-league</i></div> -->
		
			</div>
			<div class="span4">
				<div id="MatchmakingTimeNotification">
				<h4 class="blackH4">
					<i class="icon-time"></i> MATCH<green>MAKING</green>
				</h4>
<!-- 					<img src="img/find_match/mmsmall.jpg"> -->
<!-- 					<h4><i class="icon-time"></i>&nbsp;Matchmaking</h4> -->
<!-- 					<div>
						Matchmaking is every whole hour for 5 minutes. <br>Next Matchmaking will be at: <strong id="nextMatchmakingTime">{$nextHour|date_format:"%H:%M"}CET </strong> (+5 minutes)
<!-- 					</div> -->
					<div>
						Matchmaking is every minute. Next Matchmaking in <strong><span id="nextMatchmaking"></span></strong> seconds
					</div>
				</div>
	
					<h4>Searching details</h4>
					<div class="MatchMakingInfo">
						<!-- wird durch die generateSingleQueueMatchMakingInfo in matchnmakingModal.js gefaellt  -->
					</div>
				
			</div>
			<div class="span5">
				<div class="queueChat">
		        	<!-- Chat includen -->
					{include file="find_match/singleQueueNew/chat/index.tpl" chatID="findMatchChat"}
		        </div>
			</div>
		</div>
		<br />
		
		<!--       <div class="well well-small muted" style="height:100px; overflow-y:scroll;" id="wrapMatchMakingDetails"> -->
		<!-- 		<h5>Matchmaking Details</h5> -->

		<!-- 		<div id="matchmakingDetails" ></div> -->

		<!--       </div> -->
	</div>
	<div class="modal-footer">
		<button class="btn btn-danger" data-dismiss="modal" aria-hidden="true"
			id="singleQueueLeaveQueueButton">Leave Queue!</button>
	</div>
</div>
