<div class="container" style="background-color: #1A1B1D; border-top:3px solid #ea7f49">
<img src="img/frontpage-header.jpg" alt="Battlegrounds" />

{if $userLoggedIn} 
	{if $smarty.now >= $smarty.const.RELAUNCH}
<form action="{$smarty.server.PHP_SELF}?login" method="post" style="margin-bottom: -20px;">
	<div class="navbar {$hidden}"
		id="topNavi">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse"
					data-target=".nav-collapse"> <span class="icon-bar"></span> <span
					class="icon-bar"></span> <span class="icon-bar"></span>
				</a> <a class="brand" href="."><img
					src="img/dota2league-Logo-small.png" alt="Dota2 League Logo"
					width="30" style="margin-top: -5px; margin-bottom: -5px;"> <img
					alt="Dota2 League" src="img/dota2-league-text4.png"
					style="margin-top: -8px; margin-bottom: -5px;" width="79"> </a>
				<div class="nav-collapse collapse">
					<ul class="nav">

<!-- 						<li><a href="profile.php?ID={$smarty.session.user.steamID}">Profile</a></li> -->
						<li><a href="find_match.php">Find Match</a></li>
<!-- 						<li class="dropdown"> -->
<!-- 	                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Events <b class="caret"></b></a> -->
<!-- 	                        <ul class="dropdown-menu"> -->
<!-- 	                          <li><a href="events.php">Events / Tournaments <i class="icon-trophy"></i></a></li> -->
<!-- 	                          <li><a href="races.php">Races <i -->
<!-- 								class="icon-gift"></i></a></li> -->
<!-- 	                        </ul> -->
<!-- 	                      </li> -->
						
						<li><a href="ladder.php">Ladder</a></li>
						
<!-- 						<li class="dropdown"> -->
<!-- 	                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Community <b class="caret"></b></a> -->
<!-- 	                        <ul class="dropdown-menu"> -->
	                          <li><a href="http://{$smarty.server.SERVER_NAME}/forums/">Forum</a></li>
<!-- 	                          <li><a href="http://www.youtube.com/channel/UCDffhwaEawGIjOtLOTsLaNA">VODs</a></li> -->
<!-- 	                        </ul> -->
<!-- 	                      </li> -->
	                      <li class="dropdown">
	                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Help <b class="caret"></b></a>
	                        <ul class="dropdown-menu">
	                          <li><a href="help.php">FAQ</a></li>
	                          <li><a href="rules.php">Rules</a></li>
	                        </ul>
	                      </li>
					</ul>

					
					<!--               Login Form -->
					{include file='login_form.tpl'}
					<!-- Region -->
					{include "region.tpl"}
					<!--               Notifications -->
					{include "notifications.tpl"}
					
					<!-- User Coins -->
					{include file='coins.tpl' coins=$userCoins}
				</div>
			</div>
		</div>
	</div>
</form>
{else}
<form action="{$smarty.server.PHP_SELF}?login" method="post">
	<div class="navbar navbar-inverse navbar-fixed-top {$hidden}"
		id="topNavi">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse"
					data-target=".nav-collapse"> <span class="icon-bar"></span> <span
					class="icon-bar"></span> <span class="icon-bar"></span>
				</a> <a class="brand" href="."><img
					src="img/dota2league-Logo-small.png" alt="Dota2 League Logo"
					width="30" style="margin-top: -5px; margin-bottom: -5px;"> <img
					alt="Dota2 League" src="img/dota2-league-text3.png"
					style="margin-top: -8px; margin-bottom: -5px;" width="79"> </a>
				<div class="nav-collapse collapse">
					<ul class="nav">
						<li><a href="profile.php?ID={$smarty.session.user.steamID}">Profile</a></li>
						<li><a href="support.php">Support</a></li>
						<li class="dropdown"><a
							href="http://steamcommunity.com/groups/dota2-league"
							class="dropdown-toggle" data-toggle="dropdown">Steam Group <b
								class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a
									href="http://steamcommunity.com/groups/dota2-league/discussions/1/"
									target="_blank">General Discussions&nbsp;<i
										class=" icon-external-link"></i></a></li>
								<li><a
									href="http://steamcommunity.com/groups/dota2-league/discussions/0/"
									target="_blank">Bugs&nbsp;<i class=" icon-external-link"></i></a></li>
								<li><a
									href="http://steamcommunity.com/groups/dota2-league/discussions/2/"
									target="_blank">Suggestions&nbsp;<i
										class=" icon-external-link"></i></a></li>
								<li><a
									href="http://steamcommunity.com/groups/dota2-league/discussions/3/"
									target="_blank">Feedback&nbsp;<i
										class=" icon-external-link"></i></a></li>
							</ul></li>
						<li><a href="help.php">FAQ</a></li>
						<li><a href="rules.php">Rules</a></li>
						<li><a>Players already signed-in: <strong>{$userCount}</strong></a></li>

					</ul>


					<!--               Login Form -->
					{include file='login_form.tpl'}
					<!-- Region -->
					{include "region.tpl"}

				</div>
			</div>
		</div>
	</div>
</form>
{/if} {else}
<form action="{$smarty.server.PHP_SELF}?login" method="post">
	<div class="navbar navbar-inverse navbar-fixed-top {$hidden}"
		id="topNavi">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" href="."><img
					src="img/dota2league-Logo-small.png" alt="Dota2 League Logo"
					width="30" style="margin-top: -5px; margin-bottom: -5px;"> <img
					alt="Dota2 League" src="img/dota2-league-text3.png"
					style="margin-top: -8px; margin-bottom: -5px;" width="79"> </a>
				<div class="nav-collapse collapse">
					
					<ul class="nav">
					<li class="dropdown"><a
							href="http://steamcommunity.com/groups/dota2-league"
							class="dropdown-toggle" data-toggle="dropdown">Steam Group <b
								class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a
									href="http://steamcommunity.com/groups/dota2-league/discussions/1/"
									target="_blank">General Discussions&nbsp;<i
										class=" icon-external-link"></i></a></li>
								<li><a
									href="http://steamcommunity.com/groups/dota2-league/discussions/0/"
									target="_blank">Bugs&nbsp;<i class=" icon-external-link"></i></a></li>
								<li><a
									href="http://steamcommunity.com/groups/dota2-league/discussions/2/"
									target="_blank">Suggestions&nbsp;<i
										class=" icon-external-link"></i></a></li>
								<li><a
									href="http://steamcommunity.com/groups/dota2-league/discussions/3/"
									target="_blank">Feedback&nbsp;<i
										class=" icon-external-link"></i></a></li>
							</ul></li>
						<li><a>Sign-in now:</a></li>
					</ul>
				</div>
				<!--               Login Form -->
				{include file='login_form.tpl'}
				<ul class="nav pull-right">
					<li><a>Players already signed-in: <strong>{$userCount}</strong></a></li>
				</ul>
			</div>
		</div>
	</div>
</form>
{/if}

<script type="text/javascript">
	// ACTIVE MENUPUNKT dynamisch generieren
	var home_path = "{$smarty.const.HOME_PATH}";
	var url = location.href;
	l(home_path);

	//Home_Parth abschneiden
	position = url.split(home_path);
	position = position[1];

	links = $("#topNavi ul[class='nav'] a");
	l(links);
	// alle selektieren
	$(links).closest("li").removeClass("active");

	$.each(links, function(index, value) {
		l($(value).html());
		// Index.php gesondert behandelt
		if (position == "index.php" || position == "") {
			if ($(value).html() == "Home") {
				$(value).closest("li").addClass("active");
			}
		} else {
			if ($(value).attr("href") == position) {
				$(value).closest("li").addClass("active");
			}
		}
	});
	l(position);
</script>
</div>
