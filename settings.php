<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/lib/config.php');

if($_POST) {
	if(strlen($_POST['future1']) > 0) {
		$post[] = "s_future1 = ".prepare($_POST['future1']);
	}
	if(strlen($_POST['future2']) > 0) {
		$post[] = "s_future2 = ".prepare($_POST['future2']);
	}
	if(strlen($_POST['sponsor']) > 0) {
		$post[] = "s_sponsor = ".prepare($_POST['sponsor']);
	}
	if(strlen($_POST['employees']) > 0) {
		$post[] = "s_employees = ".prepare($_POST['employees']);
	}
	if(strlen($_POST['stadium_degradation']) > 0) {
		$post[] = "s_stadium_degradation = ".prepare($_POST['stadium_degradation']);
	}
	if(strlen($_POST['stadium_average']) > 0) {
		$post[] = "s_stadium_average = ".prepare($_POST['stadium_average']);
	}
	$save = mysql_query("UPDATE v_settings SET ".implode(', ',$post)." WHERE s_cookie_id = ".$unique_id.";");
	if($save) {
		$message = $lang['settings']['settings_saved'];
		$message .= (strlen($_POST['ref']) > 0 ? ' <a href="'.$_POST['ref'].'">'.$lang['settings']['go_back'].'</a>' : '');
	}
}

$page = new Page;
$settings = new Settings;
$stats = new Stats;

echo $page->header($lang['page']['settings'],3);
echo $settings->settings($message);
echo $page->footer();
$stats->count();
?>