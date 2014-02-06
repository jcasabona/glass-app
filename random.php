<?php
require_once 'config.php';
require_once 'mirror-client.php';
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_MirrorService.php';
require_once 'util.php';

$client = get_google_api_client();

// Authenticate if we're not already
if (!isset($_SESSION['userid']) || get_credentials($_SESSION['userid']) == null) {
  header('Location: ' . $base_url . '/oauth2callback.php');
  exit;
} else {
  verify_credentials(get_credentials($_SESSION['userid']));
  $client->setAccessToken(get_credentials($_SESSION['userid']));
}

// A glass service for interacting with the Mirror API
$mirror_service = new Google_MirrorService($client);

	$randMessages= array(
						array("It ain't over til it's over -Yogi Berra", "images/yogi.png", "image/png"), 
						array("Do or Do Not. There is no Try! -Yoda", "images/yoda.jpg", "image/jpeg"),
						array("Don't complain. Just work harder. -Randy Pausch", "images/randy.jpg", "image/jpeg"), 
						array("It has been my experience that folks who have no vices have very few virtues. -Abe Lincoln", "images/lincoln.jpg", "image/jpeg")
					);
					
	$index= rand(0, sizeof($randMessages)-1);	


    $new_timeline_item = new Google_TimelineItem();
    $menu_items = array();

    $menu_item = new Google_MenuItem();
    $menu_item->setAction("READ_ALOUD");
    array_push($menu_items, $menu_item);
    
    $new_timeline_item->setSpeakableText($randMessages[$index][0]);
    $new_timeline_item->setText($randMessages[$index][0]);

    $notification = new Google_NotificationConfig();
    $notification->setLevel("DEFAULT");
    $new_timeline_item->setNotification($notification);

    $new_timeline_item->setMenuItems($menu_items);

    if (isset($randMessages[$index][1]) && isset($randMessages[$index][2])) {
      insert_timeline_item($mirror_service, $new_timeline_item,
        $randMessages[$index][2], file_get_contents($randMessages[$index][1]));
    } else {
      insert_timeline_item($mirror_service, $new_timeline_item, null, null);
    }

    $message = "Quote sent: ". $randMessages[$index][0];

    print "<h3>$message</h3>";

?>