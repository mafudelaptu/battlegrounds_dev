{if $smarty.session.matchLock}
	{assign var="hidden" value="hidden"}
  {assign var="show_disabled_topnavi" value=""}
{else}
	{assign var="hidden" value=""}
  {assign var="show_disabled_topnavi" value="hidden"}
{/if}

<form action="{$smarty.server.PHP_SELF}?login" method="post">
    <div class="navbar navbar-inverse navbar-fixed-top {$hidden}" id="topNavi">
        <div class="navbar-inner">
          <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
            <a class="brand" href="."><img src="../img/dota2league-Logo-small.png" alt="Dota2 League Logo" width="30" style="margin-top:-5px; margin-bottom:-5px;">
            	<img alt="Dota2 League" src="../img/dota2-league-text3.png" style="margin-top: -8px; margin-bottom: -5px;" width="79"> ADMIN
            </a>
            <div class="nav-collapse collapse">
              <ul class="nav">
               <li><a href=".">Home</a></li>
<!--                <li><a href="tests.php">Tests</a></li> -->
<!--                <li><a href="longPolling2.php">longPolling</a></li> -->
               <li><a href="banPanel.php">Ban-Panel</a></li>
               <li><a href="matchResultPanel.php">Match-Panel</a></li>
                <li><a href="newsPanel.php">News-Panel</a></li>
                 <li><a href="notificationPanel.php">Notification-Panel</a></li>
<!--                <li><a href="test.php">test</a></li> -->
             </div>
          </div>
        </div>
      </div>
</form>

 <div class="navbar navbar-inverse navbar-fixed-top {$show_disabled_topnavi}">
        <div class="navbar-inner">
          <div class="container">
             <a class="brand">Project name</a>
            <div class="nav-collapse collapse">
             <!-- <ul class="nav">
                <li><a href=".">Home</a></li>
                <li class="active"><a href="find_match.php">Find Match</a></li>
                <li><a href="#contact">Contact</a></li>
              </ul>
              <p class="navbar-text pull-right">
                    <?php //include("inc/templates/login_form.php");?>
              </p>-->
            </div><!--/.nav-collapse -->
          </div>
        </div>
      </div>

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
	// alle delektieren
	$(links).closest("li").removeClass("active");
	
	$.each(links, function(index, value){
			l($(value).html());
			// Index.php gesondert behandelt
			if(position == "index.php" || position == ""){
				if($(value).html() == "Home"){
					$(value).closest("li").addClass("active");
				}	
			}
			else{
				if($(value).attr("href") == position){
					$(value).closest("li").addClass("active");
				}
			}
	});
	l(position);
</script>

