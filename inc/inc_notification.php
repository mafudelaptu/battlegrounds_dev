<?php 
// get Notifications
$Profile = new Profile();
$notificationData = $Profile->getNotifications($steamID);

$smarty->assign('notificationData', $notificationData['data']);
$smarty->assign('notificationCount', $notificationData['count']);
?>		
		