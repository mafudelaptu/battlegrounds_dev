<script type="text/javascript">
	var firstLogin = getParameterByName("firstLogin");
	l(firstLogin);
	if (firstLogin == 1) {
		l("hier udn so");
		$(document).ready(function() {
			$("#myModalFirstLogin").modal({
				backdrop : "static",
				keyboard : false
			});

			// Form Validierung
			$("#firstLoginForm").validate({
				rules : {
					acceptRules : {
						required : true
					}
				}
			});

		});
	}
</script>
<!-- FirstLogin -->
<form action="." method="post" id="firstLoginForm">
	<div id="myModalFirstLogin" class="modal hide fade" tabindex="-1"
		role="dialog" aria-labelledby="myModalFirstLogin" aria-hidden="true">
		<div class="modal-header">
			<h3 id="myModalFirstLoginLabel" align="center">Welcome to
				N-Gage.TV Dota2-League</h3>
		</div>
		<div class="modal-body">

			<h4>
				Hello <img src="{$smarty.session.user.avatar}" alt="Avatar"
					width="20"> {$smarty.session.user.name}!<br> You
				successfully signed in to N-Gage.TV Dota2-League
			</h4>
			<div class="row-fluid">
				<div class="span8">
					<p>
						Your Base-Points <a href="help.php#BasePoints" target="_blank"><i
							class=" icon-question-sign"></i></a> in Dota2-League are: <strong>{$stats.Points}</strong>
<!--						<i class=" icon-question-sign" onclick="$('#calcFormula').toggle()"></i> -->
					</p>
<!-- 					<div id="calcFormula" class="well well-small hide"> -->
<!-- 						<p>The Base-Points were calculated by this formula:</p> -->
<!-- 						<p> -->
<!-- 							<strong>Base-D2L-Points</strong> = (total Games played) / 10 + -->
<!-- 							Wins/10 + (Win Rate - 50) * 10 -->
<!-- 						</p> -->
<!-- 						<small class="muted"> * all underlying values come from -->
<!-- 							DotaBuff (http://dotabuff.com/) </small> -->
<!-- 					</div> -->
					<div>
						based on your Base-D2L-Points you were assigned to the <strong>{$leagueName}-Skill-Bracket</strong>
					</div>
				</div>
				<div class="span4" align="center">
					{include "prototypes/skillBracketImage.tpl" points=$stats.Points
					skillBracket=$leagueData.SkillBracketTypeID}
		
					<div class="t" title="Your League is: {$leagueName}" align="center">
						<strong>{$leagueName}</strong>
					</div>
				</div>
			</div>
			<div>
				<h4>Rules of Dota2-League</h4>
				<div style="height: 200px; overflow: auto" class="well well-small">
					{include "help/rules.tpl"}</div>
				<label class="checkbox"> <input type="checkbox" value="1"
					name="acceptRules" id="acceptRules"> I have read and accept the Rules of
					Dota2-League
				</label>
			</div>
		</div>
		<!-- ModalBody-End -->
		<div class="modal-footer">
			<button class="btn" type="button" onClick="handleMoreInfoMatch();">More
				info about D2L</button>
			<button class="btn" type="button" onClick="handleViewProfile();">View
				Profile</button>
			
			{if $smarty.now >= $smarty.const.RELAUNCH}
			<button class="btn btn-primary" type="button"
				onClick="handleFindMatch();">Alright, find a Match!</button>
			{/if}
		</div>
	</div>
</form>
