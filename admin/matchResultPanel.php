<?php
session_start();
require_once("../inc/inc_general_php_functions_for_admin.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Dota2 Lone Wolf League</title>
<?php
	require_once(DR.HOME_PATH_REL."/inc/inc_css_and_js.php");
?>
<?php

$_SESSION['debug'] = p($retB,1);
	
	
?>
</head>

<body>
  <div id="wrap">
   <?php 
	 			$smarty->display('admin/top_navi.tpl');
			?>
	<div id="globalBG">
      <div class="container">
      	<?php 
	 			$smarty->display('admin/matchResultPanel/index.tpl');
				?>
      </div> <!-- /container -->
    </div>
    </div>
    <div id="footer">
      <div class="container">
        <?php $smarty->display('footer.tpl'); ?>
      </div>
    </div>
    <?php $smarty->display('general_stuff.tpl');?>
</body>
</html>