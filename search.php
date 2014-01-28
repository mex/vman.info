<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/lib/config.php');

if(strlen($_GET['q']) > 0) {
	$search = clean_link($_GET['q']);
	if(is_numeric(substr($search,0,7))) {
		if(substr($search,0,7) > 1500000) {
			header('Location: /players/'.$search);
			exit;
		} else {
			header('Location: /clubs/'.$search);
			exit;
		}
	} else {
		$page = new Page;
		
		echo $page->header('',1);
		echo $page->search($search);;
		echo $page->footer();
	}
}
?>