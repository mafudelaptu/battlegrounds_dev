<script type="text/javascript">
	var firstLogin = getParameterByName("notAllowed");
	l(firstLogin);
	if (firstLogin == 1) {
		l("hier udn so");
		$(document).ready(function() {
			$("#notAllowedModal").modal({
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


<div id="notAllowedModal" class="modal hide fade" tabindex="-1"
	role="dialog" aria-labelledby="notAllowedModal" aria-hidden="true">
	<div class="modal-header">
		<h3 id="notAllowedModalLabel" align="center">Dota2-League is very sorry</h3>
	</div>
	<div class="modal-body">

		<h4>
			Sorry, but you dont fulfill the requirements
		</h4>
		<div>
			<p>For joining Dota2-League.net you need following requirements:</p>
			<ul>
				<li>Amount of Wins on DotaBuff.com: <span class="text-error"><strong>{$smarty.const.WINSBORDER}</strong> Wins <i class=" icon-remove"></i></span></li>
			</ul>
			
			<p>These requirements are very flexible and changes over time. So check frequently our Steam-Group <a href="http://steamcommunity.com/groups/dota2-league">Dota2-League</a> for changes. </p>
		</div>
		{**if $smarty.const.DEBUG}
			<pre>{$smarty.session.debug2}</pre>
		{/if*}
	</div>
	<!-- ModalBody-End -->
	<div class="modal-footer">
		<button class="btn" type="button" data-dismiss="modal" aria-hidden="true" onclick="window.location = 'index.php';">OK, back to Home-page</button>
	</div>
</div>

