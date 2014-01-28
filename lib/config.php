<?php
header('Content-Type:text/html;charset=utf-8');

$version = 'foxtrot (3.0.3)';

require_once($_SERVER['DOCUMENT_ROOT'].'/lib/functions.php');

define(DB_HOST, 'localhost');
define(DB_USER, 'root');
define(DB_PASS, '');
define(DB_NAME, 'vmaninfo');

$db = mysql_connect(DB_HOST,DB_USER,DB_PASS);
mysql_select_db(DB_NAME,$db);
mysql_query('SET NAMES utf8;')or die('We are experiencing problems with the database. We are working on the issue.');

if(strlen($_COOKIE['unique_id']) > 0) {
	$unique_id = prepare($_COOKIE['unique_id']);
	setcookie('unique_id',$unique_id,time()+60*60*24*30,'/');
	$fetch_user_info = mysql_fetch_array(mysql_query("SELECT s_future1,s_future2,s_sponsor,s_employees,s_stadium_degradation,s_stadium_average,s_notes FROM v_settings WHERE s_cookie_id = ".$unique_id.";"));
} else {
	$unique_id = time().rand(1000,9999);
	setcookie('unique_id',$unique_id,time()+60*60*24*30,'/');
	mysql_query("INSERT INTO v_settings (s_cookie_id) VALUES (".$unique_id.");");
}

require_once($_SERVER['DOCUMENT_ROOT'].'/lib/languages/en.php');
if(strlen($_GET['lang']) > 0) {
	setcookie('lang',$_GET['lang'],time()+60*60*24*30,'/');
	$language = $_GET['lang'];
} else if(strlen($_COOKIE['lang']) > 0) {
	setcookie('lang',$_COOKIE['lang'],time()+60*60*24*30,'/');
	$language = $_COOKIE['lang'];
} else {
	$language = 'en';
}
require_once($_SERVER['DOCUMENT_ROOT'].'/lib/languages/'.$language.'.php');
?>