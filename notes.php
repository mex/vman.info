<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/lib/config.php');

if(strlen($_POST['notes']) > 0) {
	$save = mysql_query("UPDATE v_settings SET s_notes = '".prepare($_POST['notes'])."' WHERE s_cookie_id = ".$unique_id.";");
	if($save) {
		$message = $lang['notes']['notes_saved'];
	}
}

$page = new Page;
$settings = new Settings;
$stats = new Stats;

echo $page->header($lang['page']['notes'],2);
if($_GET['edit'] == 'true') {
	echo $settings->edit_notes();
} else {
	echo $settings->view_notes($message);
}
echo $page->footer();
$stats->count();
?>