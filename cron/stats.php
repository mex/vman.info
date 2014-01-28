<?php
header('Content-Type:text/html;charset=utf-8');

define(DB_HOST, 'localhost');
define(DB_USER, 'root');
define(DB_PASS, '');
define(DB_NAME, 'vmaninfo');

$db = mysql_connect(DB_HOST,DB_USER,DB_PASS);
mysql_select_db(DB_NAME,$db);
mysql_query('SET NAMES utf8;')or die('We are experiencing problems with the database. We are working on the issue.');

$starttime = microtime();
$startarray = explode(" ", $starttime);
$starttime = $startarray[1] + $startarray[0];

$clubs = mysql_fetch_assoc(mysql_query("SELECT COUNT(c_id) AS count FROM v_clubs WHERE timestamp > '2010-01-01';"));
$players = mysql_fetch_assoc(mysql_query("SELECT COUNT(p_id) AS count FROM v_players WHERE timestamp > '2010-01-01';"));
$visitors = mysql_num_rows(mysql_query("SELECT v_id FROM v_views WHERE v_timestamp > '2010-01-01' GROUP BY v_ip;"));
$views = mysql_fetch_assoc(mysql_query("SELECT COUNT(v_id) AS count FROM v_views WHERE v_timestamp > '2010-01-01';"));

mysql_query("UPDATE v_stats SET v_value = ".$clubs['count']." WHERE v_option = 'clubs processed';");
mysql_query("UPDATE v_stats SET v_value = ".$players['count']." WHERE v_option = 'players processed';");
mysql_query("UPDATE v_stats SET v_value = ".$visitors." WHERE v_option = 'unique visitors';");
mysql_query("UPDATE v_stats SET v_value = ".$views['count']." WHERE v_option = 'views';");

mysql_query("INSERT INTO v_cron (c_file) VALUES ('".$_SERVER['REQUEST_URI']."');");

$endtime = microtime();
$endarray = explode(" ", $endtime);
$endtime = $endarray[1] + $endarray[0];
$totaltime = $endtime - $starttime;
$totaltime = round($totaltime,5);
echo 'Page loaded in '.$totaltime.' seconds';
?>