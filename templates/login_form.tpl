{if $userLoggedIn == "true"}
	{if $smarty.now >= $smarty.const.RELAUNCH}
		<ul class="nav pull-right">
	         <li id="fat-menu" class="dropdown">
	           <a href="#" id="drop3" role="button" class="dropdown-toggle" data-toggle="dropdown">
	           		<img src="{$userAvatarMed}" width="20" hspace="10">{$userName|truncate:10}
	           	<b class="caret"></b>
	           </a>
	           <ul class="dropdown-menu" role="menu" aria-labelledby="drop3">
	             <li><a tabindex="-1" href="profile.php?ID={$smarty.session.user.steamID}">Profile</a></li>
	             <li><a tabindex="-1" href="ladder.php?ID={$steamID}">Ladder</a></li>
	            {* <li class="divider"></li>
	             <li><a tabindex="-1" href="https://dotabuff.com/players/{$steamID}" target="_blank">Dotabuff-Profile</a></li>
	             <li><a tabindex="-1" href="http://steamcommunity.com/profiles/{$steamID}" target="_blank">Steam-Profile</a></li>
	             *}
	             {* <li><a href="javascript:void(0)" onclick="logout();">Logout</a></li> *}
	           </ul>
	         </li>
	     </ul>
	{else}
		<ul class="nav pull-right">
         <li id="fat-menu" class="dropdown">
           <a href="#" id="drop3" role="button" class="dropdown-toggle" data-toggle="dropdown">
           		<img src="{$userAvatarMed}" width="20" hspace="10">{$userName} 
           	<b class="caret"></b>
           </a>
           <ul class="dropdown-menu" role="menu" aria-labelledby="drop3">
             <li><a tabindex="-1" href="profile.php?ID={$steamID}">Profile</a></li>
             {*<li class="divider"></li>
             <li><a tabindex="-1" href="https://dotabuff.com/players/{$steamID}" target="_blank">Dotabuff-Profile</a></li>
             <li><a tabindex="-1" href="http://steamcommunity.com/profiles/{$steamID}" target="_blank">Steam-Profile</a></li>
             *}
            {* <li><a href="javascript:void(0)" onclick="logout();">Logout</a></li>*}
           </ul>
         </li>
     </ul>
	{/if}
{else}
	{if $smarty.now >= $smarty.const.RELAUNCH}
	<ul class="nav pull-right">
	{if $smarty.const.DEBUG OR $smarty.server.HTTP_HOST|strpos:"dota2-league" != false}
		<button class="btn" onclick="loginAsFakeUser()" type="button">Login As Fake User</button>
	{/if}
		<input class="navbar-text" type="image" src="{$smarty.const.HOME_PATH}img/sits_small.png" name="steam_signin" id="steam_signin" style="padding-top:8px;">
	</ul>
	{else}

	{if $smarty.const.DEBUG OR $smarty.server.HTTP_HOST|strpos:"dota2-league" != false}
<!-- 		<button class="btn" onclick="loginAsFakeUser()" type="button">Login As Fake User</button> -->
	{/if}
		<input class="navbar-text" type="image" src="{$smarty.const.HOME_PATH}img/sits_small.png" name="steam_signin" id="steam_signin" style="padding-top:8px;">
	{/if}

	
{/if}

 


