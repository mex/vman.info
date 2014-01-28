<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/lib/config.php');

$cid = (strlen($_GET['cid']) > 0 ? clean_link($_GET['cid']) : '');
$pid = (strlen($_GET['pid']) > 0 ? clean_link($_GET['pid']) : '');

$page = new Page;
$fetch = new Fetch;
$stats = new Stats;

if(strlen($_GET['stats']) > 0) {
	$content = $stats->visitors();
} else {
	if($cid > 0) {
		$club = $fetch->club($cid);
		$content = $club[0].$fetch->explanations();
	} elseif($pid > 0) {
		$player = $fetch->player($pid);
		$content = $player[0].$fetch->explanations();
	} elseif(strlen($_GET['cid']) > 0 || strlen($_GET['pid']) > 0) {
		$content = $page->error($_GET['cid'],$_GET['pid']);
	} else {
		$content = $page->content();
	}
}

echo $page->header(($cid > 0 ? $club[1].' ('.$cid.')' : ($pid > 0 ? $player[1].' ('.$pid.')' : '')),1);
echo $content;
echo $page->footer();
$stats->count();
?>